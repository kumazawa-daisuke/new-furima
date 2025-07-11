@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css') }}">
@endsection

@section('content')
<div class="tab-bar">
    <a href="{{ route('items.index', ['tab' => 'recommend', 'q' => request('q')]) }}" class="{{ $tab === 'recommend' ? 'active' : '' }}">
        おすすめ
    </a>
    @auth
        <a href="{{ route('items.index', ['tab' => 'mylist', 'q' => request('q')]) }}" class="{{ $tab === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    @endauth
</div>

<div class="item-list">
    @forelse($items as $item)
        <div class="item-card">
            <a href="{{ route('items.show', ['item_id' => $item->id]) }}">
                <div class="item-img-block" style="position:relative;">
                    @if(!empty($item->img_url))
                        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}" class="item-img">
                    @else
                        <div class="item-img no-image">
                            NO IMAGE
                        </div>
                    @endif
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