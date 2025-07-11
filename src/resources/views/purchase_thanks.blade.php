@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_thanks.css') }}">
@endsection

@section('content')
<div class="thanks-container">
    <div class="thanks-message">
        購入ありがとうございました
    </div>
    <a href="{{ route('items.index') }}" class="thanks-link">トップページへ戻る</a>
</div>
@endsection
