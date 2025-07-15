@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_show.css') }}">
@endsection

@section('content')
<div class="item-main">
    <div class="item-image-block" style="position:relative;">
        @if($item->img_url)
            <img src="{{ asset('storage/' . $item->img_url) }}" alt="商品画像" class="item-image">
            @if($item->status == 1)
                <span class="sold-overlay">SOLD</span>
            @endif
        @else
            <div class="item-image-placeholder">No Image</div>
        @endif
    </div>
    <div class="item-summary-block">
        <div class="item-title-block">
            <div class="item-title">{{ $item->name }}</div>
            @if(!empty($item->brand) && $item->brand !== 'なし')
                <div class="item-brand">{{ $item->brand }}</div>
            @endif
        </div>
        <div class="item-price-block">
            <span class="item-price-symbol">￥</span>
            <span class="item-price">{{ number_format($item->price) }}</span>
            <span class="item-tax">（税込）</span>
        </div>
        {{-- いいね・コメントアイコン --}}
        <div class="item-icons-block">
            {{-- いいね機能 --}}
            @php
                $liked = false;
                if (auth()->check()) {
                    $liked = $item->likes->contains('user_id', auth()->id());
                }
                $likeCount = $item->likes->count();
                $commentCount = $item->comments ? $item->comments->count() : 0;
            @endphp
            <div class="icon-set">
            <form 
                action="{{ $liked ? route('items.unlike', $item->id) : route('items.like', $item->id) }}" 
                method="POST"
                class="like-form"
            >
                @csrf
                @if($liked)
                    @method('DELETE')
                @endif
                <button type="submit" class="like-btn">
                    <img 
                        src="{{ asset('storage/images/' . ($liked ? 'red_star.png' : 'star.png')) }}"
                        alt="いいね" 
                        class="icon-img"
                    >
                </button>
                <div class="icon-num">{{ $likeCount }}</div>
            </form>
            </div>
            <div class="icon-set">
                <div class="comment-icon">
                    <img src="{{ asset('storage/images/comment.png') }}" alt="コメント" class="icon-img">
                    <div class="icon-num">{{ $commentCount }}</div>
                </div>
            </div>
        </div>
        <form action="{{ route('purchase.form', $item->id) }}" method="GET" class="mb-3">
            @if($item->user_id === auth()->id())
                <button type="button" class="btn-buy" disabled style="background:#aaa;">自分の商品です</button>
            @elseif($item->status == 1)
                <button type="button" class="btn-buy" disabled style="background:#aaa;">売り切れました</button>
            @else
                @auth
                    <button type="submit" class="btn-buy">購入手続きへ</button>
                @else
                    <a href="{{ route('login') }}" class="btn-buy" style="display:block; text-align:center;">購入手続きへ</a>
                @endauth
            @endif
        </form>

        <div class="item-desc-block">
            <h3 class="big-title">商品説明</h3>
            <div class="item-desc-text big-text">{{ $item->description }}</div>
        </div>
        <div class="item-info-block">
            <h3 class="big-title">商品の情報</h3>
            <div class="item-info-flex">
                <div class="item-info-label">カテゴリー</div>
                <div>
                    @foreach(explode(',', $item->category) as $cat)
                        <span class="item-category-badge">{{ trim($cat) }}</span>
                    @endforeach
                </div>
            </div>
            <div class="item-info-flex">
                <div class="item-info-label">商品の状態</div>
                <div class="item-condition-text">{{ $item->condition }}</div>
            </div>
        </div>
        <div class="item-comments-block">
            <h4 class="big-title">
                コメント（{{ $commentCount }}）
            </h4>
            @if($item->comments && $commentCount > 0)
            @foreach($item->comments as $comment)
                <div class="comment-flex">
                    <div class="comment-header">
                        <div class="comment-avatar">
                            @if(!empty($comment->user->profile_image))
                                <img src="{{ asset('storage/profile/'.$comment->user->profile_image) }}" alt="プロフィール画像" class="profile-avatar">
                            @else
                                <span class="default-icon"></span>
                            @endif
                        </div>
                        <span class="comment-user">{{ $comment->user->name ?? '名無し' }}</span>
                    </div>
                    <div class="comment-content">
                        {{ $comment->content }}
                    </div>
                </div>
            @endforeach
            @endif

            <div class="comment-form-section">
                <div class="comment-form-title">商品へのコメント</div>
                @auth
                <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment-form" novalidate>
                    @csrf
                    <textarea name="content" rows="7" placeholder="商品のコメント" required>{{ old('content') }}</textarea>
                    @if(isset($errors))
                        @error('content')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    @endif
                    <button type="submit" class="btn-comment">コメントを送信する</button>
                </form>
                @else
                    <div class="not-login-message">コメントするにはログインしてください。</div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
