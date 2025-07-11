<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

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
                    'unit_amount' => $item->price, // Stripeの単位に注意（既に1円単位なら*100不要）
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.thanks'),
            'cancel_url' => route('purchase.form', ['item_id' => $item->id]),
            'customer_email' => $user->email,
        ]);

        // ここでDB保存する場合、未決済データが入ることは念頭に
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $paymentMethod,
            'price' => $item->price,
            'shipping' => $validated['shipping'],
        ]);

        $item->status = 1;
        $item->save();

        return redirect($session->url);
    }

    // サンクスページ表示
    public function thanks()
    {
        return view('purchase_thanks');
    }
}
