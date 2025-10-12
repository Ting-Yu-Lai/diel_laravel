<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '會員系統')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 自訂 CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <!-- 頂部導航列 -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">MySite</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth('member')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('member.dashboard') }}">會員中心</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('member.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-link nav-link" type="submit">登出</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('member.loginForm') }}">登入</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('member.registerForm') }}">註冊</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- 主要內容 -->
    <div class="container">
        @yield('content')
    </div>

    <!-- 頁尾 -->
    <footer class="bg-light text-center text-muted py-3 mt-4">
        &copy; {{ date('Y') }} MySite. All rights reserved.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
