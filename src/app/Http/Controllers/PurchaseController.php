<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 商品購入画面表示
    public function form($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->user_id == $user->id) {
            return redirect()->route('items.show', $item_id)
                ->with('error', '自分が出品した商品は購入できません。');
        }

        return view('purchase_form', compact('item', 'user'));
    }

    // 購入処理
    public function store(PurchaseRequest $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if ($item->user_id == $user->id) {
            return redirect()->route('items.show', $item_id)
                ->with('error', '自分が出品した商品は購入できません。');
        }

        $validated = $request->validated();
        
        // Stripe初期化
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentMethod = $validated['payment_method'];
        $payment_methods = ['card'];
        if ($paymentMethod === 'コンビニ払い') {
            $payment_methods = ['konbini'];
        }

        // Stripe Checkoutセッション生成
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => $payment_methods,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // success_urlに商品IDなどの必要な情報をクエリパラメータとして渡す
            'success_url' => route('purchase.thanks', [
                'item_id' => $item->id,
                'payment_method' => $paymentMethod,
                'shipping' => $validated['shipping']
            ]),
            'cancel_url' => route('purchase.form', ['item_id' => $item->id]),
            'customer_email' => $user->email,
        ]);

        return redirect($session->url, 303);
    }

    // サンクスページ表示 & データベース保存
    public function thanks(Request $request)
    {
        // 必須パラメータのチェック
        if (!$request->has(['item_id', 'payment_method', 'shipping'])) {
            // パラメータが不足している場合、エラーとしてフォームに戻す
            return redirect()->route('items.index')->with('error', '購入情報が不正です。');
        }

        $item = Item::findOrFail($request->input('item_id'));
        $user = Auth::user();

        // 購入済みでないか確認
        if ($item->status == 1) {
            // 既に売り切れている場合は、重複して購入レコードを作成しない
            return view('purchase_thanks');
        }

        // 購入レコードを作成
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $request->input('payment_method'),
            'price' => $item->price,
            'shipping' => $request->input('shipping'),
            'status' => 'in_progress', // 新しいステータス
        ]);

        // 商品のステータスを「売り切れ」に更新
        $item->status = 1;
        $item->save();

        return view('purchase_thanks');
    }
}
