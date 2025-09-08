<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Purchase;
use App\Models\Rating;
use App\Models\Message;

class ProfileController extends Controller
{
    // マイページ
    public function index(Request $request)
{
    $user = Auth::user();
    $tab = $request->input('tab', 'sell');

    // 未読メッセージの総数と各取引の未読数を計算
    $trading_purchases = \App\Models\Purchase::whereIn('status', ['in_progress', 'awaiting_seller_rating'])
        ->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereHas('item', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        })
        ->with('messages')->get();

    $total_unread_count = 0;
    foreach ($trading_purchases as $purchase) {
        $unread_count = $purchase->messages()->where('sender_id', '!=', $user->id)->where('read_at', null)->count();
        $purchase->unread_count = $unread_count;
        $total_unread_count += $unread_count;
    }

    // ユーザーが受け取ったすべての評価を取得
    $ratings = Rating::where('rated_id', $user->id)->pluck('rating');

    // 評価の平均値を計算し、小数点以下を四捨五入
    $averageRating = null;
    if ($ratings->isNotEmpty()) {
        $averageRating = round($ratings->avg());
    }
    
    if ($tab === 'buy') {
        // 購入した商品のリスト
        $items = $user->purchases()->with('item')->get()->pluck('item')->filter();
    } elseif ($tab === 'trading') {
        // 取引中の商品のリストを取得し、メッセージの並び替えと未読数の計算をまとめて行う
        $items = Purchase::whereIn('status', ['in_progress', 'awaiting_seller_rating'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('item', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            // 最新メッセージのcreated_atで並び替える
            ->orderByDesc(
                \App\Models\Message::select('created_at')
                    ->whereColumn('purchase_id', 'purchases.id')
                    ->latest()
                    ->limit(1)
            )
            ->with(['item', 'messages'])->get();
        
        // 未読メッセージ数を各$itemsに反映
        foreach($items as $item) {
            $item->unread_count = $trading_purchases->firstWhere('id', $item->id)->unread_count ?? 0;
        }

    } else {
        // 出品中の商品
        $items = $user->sellingItems()->with(['likes', 'comments'])->get();
    }

    return view('profile', [
        'user' => $user,
        'tab' => $tab,
        'items' => $items,
        'total_unread_count' => $total_unread_count,
        'averageRating' => $averageRating,
    ]);
}

    // プロフィール編集画面
    public function edit()
    {
        $user = \App\Models\User::with('address')->find(Auth::id());
        return view('profile_edit', compact('user'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // ユーザー名更新
        $user->name = $request->input('name');

        // プロフィール画像アップロード
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除（必要なら）
            if ($user->profile_image && \Storage::disk('public')->exists('profile/'.$user->profile_image)) {
                \Storage::disk('public')->delete('profile/'.$user->profile_image);
            }
            // 新しい画像を保存
            $filename = uniqid().'_'.$request->file('profile_image')->getClientOriginalName();
            $request->file('profile_image')->storeAs('public/profile', $filename);
            $user->profile_image = $filename;
        }

        $user->save();
        
        // 住所はaddressesテーブル
        $isFirstAddress = false;
        $address = $user->address;
        if (!$address) {
            $address = new \App\Models\Address();
            $address->user_id = $user->id;
            $isFirstAddress = true;
        }
        $address->postal_code = $request->input('zipcode');
        $address->address = $request->input('address');
        $address->building = $request->input('building');
        $address->save();

        // 初回登録時のみ商品一覧へ、それ以外はマイページ
        if ($isFirstAddress) {
            return redirect()->route('items.index')->with('success', 'プロフィールを登録しました');
        } else {
            return redirect()->route('profile.index')->with('success', 'プロフィールを更新しました');
        }
    }
}
