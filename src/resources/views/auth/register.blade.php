@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-main">
    <h2 class="register-title">会員登録</h2>
    <form action="{{ route('register') }}" method="POST" class="register-form" novalidate>
        @csrf
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}">
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}">
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" required>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>確認用パスワード</label>
            <input type="password" name="password_confirmation" class="form-input">
        </div>
        <button type="submit" class="register-btn">登録する</button>
    </form>
    <div class="login-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
</div>
@endsection
