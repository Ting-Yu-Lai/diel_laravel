<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DielBeauty 會員中心')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Noto+Serif+TC:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/icons/Logo.webp') }}" alt="DielBeauty" height="40">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth('member')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}"
                           href="{{ route('member.dashboard') }}">首頁</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.profile*') ? 'active' : '' }}"
                           href="{{ route('member.profile') }}">個人資料</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.treatments') ? 'active' : '' }}"
                           href="{{ route('member.treatments') }}">療程紀錄</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.followUps') ? 'active' : '' }}"
                           href="{{ route('member.followUps') }}">術後追蹤</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <form action="{{ route('member.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-gold btn-sm px-3">登出</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('member.loginForm') }}">登入</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-gold btn-sm px-3" href="{{ route('member.registerForm') }}">註冊</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="container py-5">
    @yield('content')
</main>

<footer class="luxury-footer mt-auto">
    &copy; DielBeauty 2026. 晝夜新美學
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('modals')
@stack('scripts')
</body>
</html>
