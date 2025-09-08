@component('mail::message')
# 取引完了のお知らせと評価のお願い

{{ $purchase->item->user->name }} 様、

購入者様が商品「**{{ $purchase->item->name }}**」の取引を完了しました。

お手数ですが、取引チャット画面へ進んで購入者様の評価をお願いいたします。

今後ともフリマアプリをよろしくお願いいたします。

@endcomponent