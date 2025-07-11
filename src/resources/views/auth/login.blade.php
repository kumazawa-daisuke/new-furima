@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<h2 class="login-title">ログイン</h2>
<form action="{{ route('login') }}" method="POST" class="login-form" novalidate>
    @csrf
    <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
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
    <button type="submit" class="login-btn">ログイン</button>
</form>
<div class="register-link">
    <a href="{{ route('register') }}">会員登録はこちら</a>
</div>
@endsection
