<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-1" href="{{ route('admin.index') }}">
        <img src="{{ asset('images/icons/Logo.webp') }}" alt="logo" class="logo-img px-1" height="30">
        晝夜 - 後台管理
    </a>
    <input class="form-control form-control-dark w-100" type="text" placeholder="Search">
    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3" href="{{ route('admin.logout') }}">登出</a>
        </div>
    </div>
</header>
