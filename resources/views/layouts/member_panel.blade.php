<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DielBeauty 會員中心')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/backend_css.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

{{-- ── 頂部導覽列 ── --}}
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    {{-- 行動版側邊欄 toggle --}}
    <button class="btn btn-dark d-md-none px-3 border-end border-secondary"
            id="sidebarToggle" type="button" aria-label="選單">
        <i class="fa-solid fa-bars"></i>
    </button>

    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 d-flex align-items-center gap-2"
       href="{{ route('front.index') }}">
        <img src="{{ asset('images/icons/Logo.webp') }}" alt="DielBeauty" height="28">
        <span>新美學 — 會員中心</span>
    </a>
    <div class="navbar-nav flex-row align-items-center">
        @auth('member')
            <span class="nav-link px-3 text-white-50 small d-none d-md-inline">
                <i class="fa-solid fa-user me-1"></i>{{ Auth::guard('member')->user()->full_name }}
            </span>
            <form action="{{ route('member.logout') }}" method="POST" class="d-inline me-2" id="logoutForm">
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
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1">
                    <span>主要功能</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}"
                           href="{{ route('member.dashboard') }}">
                            <i class="fa-solid fa-house me-1"></i> 會員首頁
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.profile') ? 'active' : '' }}"
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
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.points*') ? 'active' : '' }}"
                           href="{{ route('member.points') }}">
                            <i class="fa-solid fa-coins me-1"></i> 點數中心
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                    <span>帳號</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.security') ? 'active' : '' }}"
                           href="{{ route('member.security') }}">
                            <i class="fa-solid fa-shield-halved me-1"></i> 帳號安全
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
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>

    </div>
</div>

{{-- ── Session 逾時提示 Modal ── --}}
<div class="modal fade" id="sessionTimeoutModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-1">
                <h5 class="modal-title">
                    <i class="fa-solid fa-clock text-warning me-2"></i>工作階段即將到期
                </h5>
            </div>
            <div class="modal-body">
                您的工作階段將於 <strong id="sessionCountdown"></strong> 分鐘後自動登出。<br>
                <small class="text-muted">點選「繼續使用」以延續您的工作階段。</small>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary px-4" id="sessionExtendBtn">繼續使用</button>
                <button type="button" class="btn btn-outline-secondary" id="sessionLogoutBtn">立即登出</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── 行動版側邊欄 toggle ──
(function () {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarMenu');
    if (!toggle || !sidebar) return;
    toggle.addEventListener('click', function () {
        sidebar.classList.toggle('show');
    });
    // 點選選單項目後自動收起
    sidebar.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 768) sidebar.classList.remove('show');
        });
    });
})();

// ── 表單防重複提交 ──
(function () {
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('[type=submit]').forEach(function (btn) {
                btn.disabled = true;
                if (btn.dataset.loadingText) btn.textContent = btn.dataset.loadingText;
                else if (btn.textContent.trim()) btn.textContent = '處理中…';
            });
        });
    });
})();

// ── Session 逾時倒數警告 ──
(function () {
    const lifetimeMs  = {{ config('session.lifetime') }} * 60 * 1000;
    const warningMs   = 5 * 60 * 1000;
    const warnAt      = lifetimeMs - warningMs;
    const modal       = new bootstrap.Modal(document.getElementById('sessionTimeoutModal'));
    const countdown   = document.getElementById('sessionCountdown');
    const extendBtn   = document.getElementById('sessionExtendBtn');
    const logoutBtn   = document.getElementById('sessionLogoutBtn');
    const logoutForm  = document.getElementById('logoutForm');

    let warningShown  = false;
    let intervalId    = null;
    let minutesLeft   = 5;
    const pageLoadAt  = Date.now();

    // 顯示 Modal 並開始每分鐘倒數
    function showWarning() {
        warningShown = true;
        countdown.textContent = minutesLeft;
        modal.show();
        intervalId = setInterval(function () {
            minutesLeft--;
            if (minutesLeft <= 0) {
                clearInterval(intervalId);
                modal.hide();
                window.location.href = '{{ route('member.loginForm') }}';
            } else {
                countdown.textContent = minutesLeft;
            }
        }, 60 * 1000);
    }

    // 距上次頁面載入多久後觸發警告
    const delay = warnAt - (Date.now() - pageLoadAt);
    if (delay > 0) {
        setTimeout(showWarning, delay);
    } else if (!warningShown) {
        showWarning();
    }

    // 繼續使用：對 dashboard 發 fetch 以重置 session，並重置計時器
    extendBtn.addEventListener('click', function () {
        clearInterval(intervalId);
        modal.hide();
        warningShown = false;
        minutesLeft  = 5;
        fetch('{{ route('member.dashboard') }}', { credentials: 'same-origin' });
        setTimeout(showWarning, warnAt);
    });

    logoutBtn.addEventListener('click', function () {
        clearInterval(intervalId);
        modal.hide();
        if (logoutForm) logoutForm.submit();
    });
})();
</script>

@stack('scripts')
</body>
</html>
