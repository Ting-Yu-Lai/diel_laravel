<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DielBeauty 會員中心')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/backend_css.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

{{-- ── 頂部導覽列 ── --}}
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 d-flex align-items-center gap-2"
       href="{{ route('front.index') }}">
        <img src="{{ asset('images/icons/Logo.webp') }}" alt="DielBeauty" height="28">
        <span>晝夜 — 會員中心</span>
    </a>
    <div class="navbar-nav flex-row align-items-center">
        @auth('member')
            <span class="nav-link px-3 text-white-50 small d-none d-md-inline">
                <i class="fa-solid fa-user me-1"></i>{{ Auth::guard('member')->user()->full_name }}
            </span>
            <form action="{{ route('member.logout') }}" method="POST" class="d-inline me-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light px-3">登出</button>
            </form>
        @endauth
    </div>
</header>

<div class="container-fluid">
    <div class="row">

        {{-- ── 左側選單 ── --}}
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}"
                           href="{{ route('member.dashboard') }}">
                            <i class="fa-solid fa-house me-1"></i> 會員首頁
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.profile*') ? 'active' : '' }}"
                           href="{{ route('member.profile') }}">
                            <i class="fa-solid fa-user me-1"></i> 個人資料
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.treatments') ? 'active' : '' }}"
                           href="{{ route('member.treatments') }}">
                            <i class="fa-solid fa-clipboard-list me-1"></i> 療程紀錄
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.followUps') ? 'active' : '' }}"
                           href="{{ route('member.followUps') }}">
                            <i class="fa-solid fa-heart me-1"></i> 術後追蹤
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                    <span>其他</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.index') }}">
                            <i class="fa-solid fa-arrow-left me-1"></i> 回官網首頁
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- ── 主內容區 ── --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
