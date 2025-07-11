@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-content">
    <h2>登録していただいたメールアドレスに認証メールを送信しました。<br>メール認証を完了してください。</h2>
    @if (session('message'))
        <div class="alert-success">{{ session('message') }}</div>
    @endif

    <a href="http://localhost:8025" class="verify-btn" target="_blank" rel="noopener noreferrer">
        認証はこちらから
    </a>

    <div class="verify-resend">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="verify-resend-link">
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection
