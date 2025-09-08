<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>フリマアプリ</title>
  <link rel="stylesheet" href="{{ asset('css/header.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  @yield('css')
</head>

@php
    $routeName = Route::currentRouteName();
    $hideNavRoutes = [
        'login', 
        'register', 
        'verification.notice',       // メール認証案内ページ
        'verification.send',         // メール認証再送
        'verification.verify',       // メール認証本体
        'chat',
    ];
@endphp

<body>
    <header class="global-header">
        <div class="header-inner">
            <a href="/" class="logo">
                <img src="{{ asset('storage/images/logo.svg') }}" alt="COACHTECH" class="logo-img">
            </a>
            @if (!in_array($routeName, $hideNavRoutes))
                <form action="/search" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="なにをお探しですか？" value="{{ request('q') }}">
                </form>
                <nav class="header-nav">
                    @if(Auth::check())
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit" class="nav-link logout-btn">ログアウト</button>
                        </form>
                        <a href="/mypage" class="nav-link">マイページ</a>
                        <a href="/sell" class="nav-btn">出品</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">ログイン</a>
                        <a href="/mypage" class="nav-link">マイページ</a>
                        <a href="/sell" class="nav-btn">出品</a>
                    @endif
                </nav>
            @endif
        </div>
    </header>

  <main>
    @yield('content')
  </main>
</body>
</html>
