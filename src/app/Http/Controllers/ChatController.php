<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreChatMessageRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\RatingRequestMail;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        $user = Auth::user();

        if ($purchase->item->user_id !== $user->id && $purchase->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // ログインユーザーが受信した未読メッセージを既読にする
        $purchase->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $purchase->messages()->with('sender')->get();

        $chatPartner = ($purchase->item->user_id === $user->id)
            ? $purchase->user
            : $purchase->item->user;
    
        $isTransactionCompleted = $purchase->status === 'completed' || $purchase->status === 'awaiting_seller_rating';

        $otherPurchases = Purchase::with(['item', 'item.user', 'user'])
            ->where(function ($query) use ($user) {
                $query->whereHas('item', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->orWhere('user_id', $user->id);
            })
            ->where('id', '!=', $purchase->id)
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('purchase_id', 'purchases.id')
                    ->latest()
                    ->limit(1)
            )
            ->get();

        return view('chat', compact('purchase', 'messages', 'chatPartner', 'otherPurchases', 'isTransactionCompleted'));
    }


    public function store(StoreChatMessageRequest $request, Purchase $purchase)
    {
        $user = Auth::user();

        if ($purchase->item->user_id !== $user->id && $purchase->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $message = new Message();
        $message->purchase_id = $purchase->id;
        $message->sender_id = $user->id;

        // 画像のアップロード処理
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('messages', 'public');
            $message->image_url = 'storage/' . $path;
            $message->content = $request->input('content') ?? '(画像)';
        } else {
            $message->content = $request->input('content');
        }

        $message->save();

        return back()->with('success', 'メッセージを送信しました');
    }

    public function destroy(Message $message)
    {
        // 投稿者本人であるか、または出品者/購入者であるかを確認
        $user = Auth::user();
        if ($message->sender_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // 画像があれば削除
        if ($message->image_url) {
            Storage::disk('public')->delete(str_replace('storage/', '', $message->image_url));
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }

    public function update(Request $request, Message $message)
    {
        $user = Auth::user();
        if ($message->sender_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message->update($validated);
        
        return back()->with('success', 'メッセージを更新しました。');
    }

    public function completeTransaction(Request $request, Purchase $purchase)
    {
        // ログインユーザーが購入者であることを確認
        if ($purchase->user_id !== Auth::id()) {
            return back()->with('error', '取引を完了する権限がありません。');
        }

        // 既に完了している場合は何もしない
        if ($purchase->status === 'completed' || $purchase->status === 'awaiting_seller_rating') {
            return back()->with('error', 'この取引は既に完了処理中です。');
        }

        // 取引ステータスを「出品者の評価待ち」に更新
        $purchase->status = 'awaiting_seller_rating';
        $purchase->save();

        // 出品者へ評価を促すメールを送信
        $seller = $purchase->item->user;
        Mail::to($seller->email)->send(new RatingRequestMail($purchase));

        return redirect()->route('chat', ['purchase' => $purchase->id])->with('success', '取引を完了しました。出品者の評価が完了すると、取引が正式に完了となります。');
    }
}
