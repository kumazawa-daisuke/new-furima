<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        // リクエストのバリデーション
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'rated_id' => 'required|exists:users,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        $purchase = Purchase::findOrFail($request->input('purchase_id'));
    
        // ログインユーザーが評価する権限があるか、かつ評価済みでないかを確認
        if ($purchase->item->user_id !== $user->id && $purchase->user_id !== $user->id) {
            return back()->with('error', 'この取引の評価権限がありません。');
        }

        // 既に評価済みかチェック
        $existingRating = Rating::where('purchase_id', $purchase->id)
                                ->where('rater_id', $user->id)
                                ->first();

        if ($existingRating) {
            return back()->with('error', 'この取引は既に評価済みです。');
        }

        // 評価をデータベースに保存
        Rating::create([
            'purchase_id' => $purchase->id,
            'rater_id' => $user->id,
            'rated_id' => $request->input('rated_id'),
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        // 評価投稿後のステータス更新
        // 評価したのが出品者であれば、取引を最終完了にする
        if ($user->id === $purchase->item->user_id) {
            $purchase->status = 'completed';
            $purchase->save();
        }
        // 評価したのが購入者であれば、出品者の評価待ちにする
        elseif ($user->id === $purchase->user_id) {
            $purchase->status = 'awaiting_seller_rating';
            $purchase->save();
        }
    
        // 評価投稿後は商品一覧ページにリダイレクト
        return redirect()->route('items.index')->with('success', '評価を投稿しました。');
    }
}
