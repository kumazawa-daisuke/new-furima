@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
@endsection

@section('content')
    <div class="profile-header">
        @if(!empty($user->profile_image))
            <img src="{{ asset('storage/profile/'.$user->profile_image) }}" alt="プロフィール画像" class="profile-avatar">
        @else
            <i class="fa-solid fa-user-circle default-icon"></i>
        @endif
        
        <div class="profile-name-and-rating">
            <div class="profile-username">{{ $user->name }}</div>
            @if (isset($averageRating))
                <div class="average-rating-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $averageRating)
                            <i class="fa-solid fa-star" style="color:#fdd835;"></i>
                        @else
                            <i class="fa-regular fa-star" style="color:#d9d9d9;"></i>
                        @endif
                    @endfor
                </div>
            @endif
        </div>
        
        <a href="{{ route('profile.edit') }}" class="profile-edit-btn">プロフィールを編集</a>
    </div>
    
    <div class="profile-tabs">
        <a href="{{ route('profile.index', ['tab' => 'sell']) }}" class="{{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('profile.index', ['tab' => 'buy']) }}" class="{{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ route('profile.index', ['tab' => 'trading']) }}" class="tab-trading {{ $tab === 'trading' ? 'active' : '' }}">
            取引中の商品
            @if ($total_unread_count > 0)
                <div class="notification-count">{{ $total_unread_count }}</div>
            @endif
        </a>
    </div>

    <div class="profile-items">
        @forelse($items as $item)
            @if ($tab === 'trading')
                <div class="item-card">
                    <a href="{{ route('chat', ['purchase' => $item->id]) }}">
                        <div class="item-img-block" style="position:relative;">
                            <img src="{{ asset('storage/' . $item->item->img_url) }}" alt="{{ $item->item->name }}" class="item-img">
                            @if (isset($item->unread_count) && $item->unread_count > 0)
                                <div class="notification-badge">{{ $item->unread_count }}</div>
                            @endif
                        </div>
                    </a>
                    <div class="item-name">{{ $item->item->name }}</div>
                </div>
            @else
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
            @endif
        @empty
            <p>商品がありません。</p>
        @endforelse
    </div>
@endsection