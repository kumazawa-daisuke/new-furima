@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-header">
        @if(!empty($user->profile_image))
            <img src="{{ asset('storage/profile/'.$user->profile_image) }}" alt="プロフィール画像" class="profile-avatar">
        @else
            <span class="default-icon"></span>
        @endif
        <div class="profile-username">{{ $user->name }}</div>
        <a href="{{ route('profile.edit') }}" class="profile-edit-btn">プロフィールを編集</a>
    </div>
    <div class="profile-tabs">
        <a href="{{ route('profile.index', ['tab' => 'sell']) }}" class="{{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('profile.index', ['tab' => 'buy']) }}" class="{{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="profile-items">
        @forelse($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', ['item_id' => $item->id]) }}">
                    <div class="item-img-block" style="position:relative;">
                        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}" class="item-img">
                        @if($item->status == 1)
                            <span class="sold-overlay">SOLD</span>
                        @endif
                    </div>
                </a>
                <div class="item-name">{{ $item->name }}</div>
            </div>
        @empty
            <p>商品がありません。</p>
        @endforelse
    </div>
@endsection