<header class="navbar navbar-expand-md navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('front.index') }}">
            <img src="{{ asset('images/icons/Logo.webp') }}" alt="DielBeauty" class="logo-img">
            <span class="ms-2" style="font-family:'Cormorant Garamond',serif; font-size:1.1rem; letter-spacing:0.12em; color:var(--milk-white);">晝夜</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        首頁
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('front.index') }}">回最上方</a></li>
                        <li><a class="dropdown-item" href="{{ route('front.index') }}#gallery">今日甜點</a></li>
                        <li><a class="dropdown-item" href="{{ route('front.index') }}#menu">菜單</a></li>
                        <li><a class="dropdown-item" href="{{ route('front.index') }}#highlights">活動剪影</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" onclick="undeveloped()" href="#">商城</a></li>
                <li class="nav-item"><a class="nav-link" onclick="undeveloped()" href="#">線上預約</a></li>

                @auth('member')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}"
                           href="{{ route('member.dashboard') }}">
                            <i class="fa-regular fa-user me-1" style="font-size:0.85rem;"></i>{{ Auth::guard('member')->user()->full_name }}
                        </a>
                    </li>
                    <li class="nav-item ms-md-2 d-flex align-items-center">
                        <form action="{{ route('member.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm px-3">登出</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('member.loginForm') }}">會員登入</a>
                    </li>
                @endauth

                <li class="nav-item"><a class="nav-link" onclick="undeveloped()" href="#">購物車</a></li>
            </ul>
        </div>
    </div>
</header>
<script>
    function undeveloped() {
        alert("功能開發中");
    }
</script>
