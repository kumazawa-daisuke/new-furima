@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-container">
    <div class="sidebar">
        <h2 class="sidebar-title">その他の取引</h2>
        <div class="other-transactions">
            @foreach ($otherPurchases as $otherPurchase)
                <a href="{{ route('chat', ['purchase' => $otherPurchase->id]) }}" class="transaction-link">
                    <p class="transaction-item-name">{{ $otherPurchase->item->name }}</p>
                </a>
            @endforeach
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <div class="chat-header-profile">
                @if(!empty($chatPartner->profile_image))
                    <img src="{{ asset('storage/profile/'.$chatPartner->profile_image) }}" alt="プロフィール画像" class="chat-partner-avatar">
                @else
                    <span class="chat-partner-avatar-default"></span>
                @endif
                <h2 class="chat-title">「{{ $chatPartner->name }}」さんとの取引画面</h2>
            </div>

            @php
                $isTransactionCompleted = $purchase->status === 'completed' || $purchase->status === 'awaiting_seller_rating';
                $isBuyer = Auth::id() === $purchase->user_id;
            @endphp

            @if ($isTransactionCompleted && $isBuyer)
                <div class="completed-message">
                    取引は完了しています
                </div>
            @elseif (!$isTransactionCompleted && $isBuyer)
                <form id="complete-transaction-form" action="{{ route('chat.complete', ['purchase' => $purchase->id]) }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <button type="button" class="complete-btn" id="complete-transaction-btn">取引を完了する</button>
            @endif
        </div>

        <div class="item-info">
            <img src="{{ asset('storage/' . $purchase->item->img_url) }}" alt="商品画像" class="item-image">
            <div class="item-details">
                <p class="item-name">{{ $purchase->item->name }}</p>
                <p class="item-price">￥{{ number_format($purchase->item->price) }}</p>
            </div>
        </div>

        <div class="message-area">
            @foreach ($messages as $message)
                <div class="message-row {{ $message->sender_id === Auth::id() ? 'my-message' : 'other-message' }}">
                    <div class="message-display-container">
                        <div class="message-body-container {{ $message->sender_id === Auth::id() ? 'my-message-body-container' : 'other-message-body-container' }}">
                            <div class="message-user-info">
                                @if(!empty($message->sender->profile_image))
                                    <img src="{{ asset('storage/profile/'.$message->sender->profile_image) }}" alt="プロフィール画像" class="message-user-avatar">
                                @else
                                    <span class="message-user-avatar-default"></span>
                                @endif
                                <div class="message-username">{{ $message->sender->name }}</div>
                            </div>
                            <div class="message-content">
                                @if(!empty($message->image_url))
                                    <img src="{{ asset($message->image_url) }}" alt="添付画像" class="message-image">
                                @endif
                                <p class="message-text">{{ $message->content }}</p>
                            </div>
                        </div>
                    </div>
                    @if ($message->sender_id === Auth::id())
                        <div class="message-actions">
                            <button class="edit-btn" data-message-id="{{ $message->id }}">編集</button>
                            <form action="{{ route('chat.destroy', ['message' => $message->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn" onclick="return confirm('本当に削除しますか？');">削除</button>
                            </form>
                        </div>
                    @endif

                    @if ($message->sender_id === Auth::id())
                        <form action="{{ route('chat.update', ['message' => $message->id]) }}" method="POST" class="edit-form" style="display:none;">
                            @csrf
                            @method('PUT')
                            <textarea name="content" class="edit-textarea">{{ $message->content }}</textarea>
                            <button type="submit" class="save-btn">保存</button>
                            <button type="button" class="cancel-btn">キャンセル</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const messageArea = document.querySelector('.message-area');
                const messageInput = document.querySelector('.chat-form textarea[name="content"]');
                const storageKey = 'chatMessage_' + window.location.pathname;

                if (sessionStorage.getItem(storageKey)) {
                    messageInput.value = sessionStorage.getItem(storageKey);
                }

                messageInput.addEventListener('input', function() {
                    sessionStorage.setItem(storageKey, this.value);
                });

                document.querySelector('.chat-form').addEventListener('submit', function() {
                    sessionStorage.removeItem(storageKey);
                });

                messageArea.addEventListener('click', function (e) {
                    const targetBtn = e.target.closest('.edit-btn, .cancel-btn');
                    if (!targetBtn) {
                        return;
                    }
                    const messageRow = targetBtn.closest('.message-row');
                    if (!messageRow) {
                        return;
                    }
                    const messageContent = messageRow.querySelector('.message-content');
                    const messageActions = messageRow.querySelector('.message-actions');
                    const editForm = messageRow.querySelector('.edit-form');
                    if (targetBtn.classList.contains('edit-btn')) {
                        messageContent.style.display = 'none';
                        messageActions.style.display = 'none';
                        editForm.style.display = 'flex';
                        editForm.querySelector('.edit-textarea').focus();
                    } else if (targetBtn.classList.contains('cancel-btn')) {
                        messageContent.style.display = 'block';
                        messageActions.style.display = 'flex';
                        editForm.style.display = 'none';
                    }
                });

                const addImageBtn = document.querySelector('.add-image-btn');
                const hiddenFileInput = document.querySelector('.hidden-file-input');

                const fileNameDisplay = document.createElement('span');
                fileNameDisplay.classList.add('file-name-display');
                addImageBtn.parentNode.insertBefore(fileNameDisplay, addImageBtn.nextSibling);

                hiddenFileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        fileNameDisplay.textContent = this.files[0].name;
                    } else {
                        fileNameDisplay.textContent = '';
                    }
                });

                const completeBtn = document.getElementById('complete-transaction-btn');
                const completeForm = document.getElementById('complete-transaction-form');

                if (completeBtn) {
                    completeBtn.addEventListener('click', function() {
                        if (confirm('取引を完了しますか？')) {
                            completeForm.submit();
                        }
                    });
                }
            });
        </script>

        @if (!$isTransactionCompleted)
            <div class="chat-input-area">
                @if ($errors->any())
                    <div class="validation-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('chat.store', ['purchase' => $purchase->id]) }}" method="POST" class="chat-form" enctype="multipart/form-data">
                    @csrf
                    <textarea name="content" placeholder="取引メッセージを入力してください">{{ old('content') }}</textarea>
                    <label for="image_upload" class="add-image-btn">画像を追加</label>
                    <input type="file" id="image_upload" name="image" class="hidden-file-input">
                    <button type="submit" class="send-btn">
                        <img src="{{ asset('storage/images/send_message.jpg') }}" alt="送信" class="send-icon">
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@php
    // ログインユーザーがまだ評価していないかチェック
    $hasRated = \App\Models\Rating::where('purchase_id', $purchase->id)
                                 ->where('rater_id', Auth::id())
                                 ->exists();

    // モーダルを表示すべきかどうかを判断
    $showRatingModal = !$hasRated && ($purchase->status === 'awaiting_seller_rating' || $purchase->status === 'completed');
@endphp

@if ($showRatingModal)
    <div id="rating-modal" class="rating-modal-overlay">
        <div class="rating-modal-content">
            <p class="rating-complete-title">取引が完了しました。</p>
            <p class="rating-question">今回の取引相手はどうでしたか？</p>
            <form action="{{ route('ratings.store') }}" method="POST" class="rating-form-design">
                @csrf
                <input type="hidden" name="rated_id" value="{{ $chatPartner->id }}">
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                <div class="rating-stars-design">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star-{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required />
                        <label for="star-{{ $i }}" title="{{ $i }} star">&#9733;</label>
                    @endfor
                </div>
                <div class="rating-divider"></div>
                <button type="submit" class="submit-rating-btn-design">送信する</button>
            </form>
        </div>
    </div>
@endif
@endsection