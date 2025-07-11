@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_form.css') }}">
@endsection

@section('content')
<form action="{{ route('purchase.store', $item->id) }}" method="POST" novalidate>
    @csrf
    <input type="hidden" name="payment_method" id="selected-payment-method">
    <div class="purchase-container">
        <div class="purchase-left">
            <div class="purchase-product-row">
                <div class="purchase-img-block" style="position: relative;">
                    <img src="{{ asset('storage/' . $item->img_url) }}" class="purchase-img" alt="商品画像">
                    @if($item->status == 1)
                        <span class="sold-overlay">SOLD</span>
                    @endif
                </div>
                <div class="purchase-title-block">
                    <div class="purchase-title">{{ $item->name }}</div>
                    <div class="purchase-price">¥{{ number_format($item->price) }}</div>
                </div>
            </div>
            <hr class="purchase-divider">

            <div class="purchase-method-row">
                <label for="payment_method" class="purchase-method-label">支払い方法</label>
                <div class="purchase-method-select-wrapper">
                    <select name="payment_method" id="payment_method" class="purchase-select" required>
                        <option value="">選択してください</option>
                        <option value="コンビニ払い" {{ old('payment_method') == 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="クレジットカード" {{ old('payment_method') == 'クレジットカード' ? 'selected' : '' }}>クレジットカード</option>
                    </select>
                </div>
                @error('payment_method')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <hr class="purchase-divider">

            <div class="purchase-address-row">
                <div class="purchase-address-header">
                    <span class="purchase-address-label">配送先</span>
                    <a href="{{ route('address.edit', $item->id) }}" class="purchase-address-edit-btn">変更する</a>
                </div>
                <div class="purchase-address-content-block">
                    @php
                        $shipping_value = (!empty($user->address->postal_code) && !empty($user->address->address))
                            ? $user->address->postal_code . ' ' . $user->address->address . ' ' . ($user->address->building ?? '')
                            : '';
                    @endphp
                    <input type="hidden" name="shipping" value="{{ $shipping_value }}">
                    <span class="purchase-address-content">
                        @if($shipping_value)
                            〒{{ $user->address->postal_code }}<br>
                            {{ $user->address->address }} {{ $user->address->building ?? '' }}
                        @else
                            <span style="color:#e33;">配送先住所が未登録です。<br>
                                <a href="{{ route('profile.edit') }}" style="color:#2176ff; text-decoration:underline;">マイページで登録してください</a>
                            </span>
                        @endif
                    </span>
                </div>
                @error('shipping')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <hr class="purchase-divider">
        </div>

        <div class="purchase-right">
            <div class="purchase-summary-table">
                <div class="summary-row">
                    <div class="summary-label summary-unbold">商品代金</div>
                    <div class="summary-value">¥{{ number_format($item->price) }}</div>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row">
                    <div class="summary-label summary-unbold">支払い方法</div>
                    <div class="summary-value" id="summary-payment-method">
                        {{ old('payment_method') ? old('payment_method') : '選択してください' }}
                    </div>
                </div>
            </div>
            @if($item->status == 1)
                <button type="button" class="purchase-btn" disabled style="background:#aaa;">売り切れました</button>
            @else
                <button type="submit" class="purchase-btn">購入する</button>
            @endif
        </div>
    </div>
</form>
<script>
document.getElementById('payment_method').addEventListener('change', function() {
    document.getElementById('summary-payment-method').textContent = this.value ? this.value : "選択してください";
    document.getElementById('selected-payment-method').value = this.value;
});
</script>
@endsection
