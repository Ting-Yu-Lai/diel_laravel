<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}"
                    href="{{ route('admin.index') }}">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/carousel*') ? 'active' : '' }}"
                    href="{{ route('admin.carousel') }}">
                    <i class="fa-solid fa-image"></i> 輪播圖管理
                </a>
            </li>
            <li class="nav-item">
                {{-- <a class="nav-link {{ request()->is('admin/dessert*') ? 'active' : '' }}"
                    href="{{ route('admin.dessert') }}">
                    <i class="fa-solid fa-cake-candles"></i> 今日甜點管理
                </a> --}}
            </li>
            <li class="nav-item">
                {{-- <a class="nav-link {{ request()->is('admin/menu*') ? 'active' : '' }}" href="{{ route('admin.menu') }}">
                    <i class="fa-solid fa-bars"></i> 菜單管理
                </a> --}}
            </li>
            <li class="nav-item">
                {{-- <a class="nav-link {{ request()->is('admin/highlight*') ? 'active' : '' }}"
                    href="{{ route('admin.highlight') }}">
                    <i class="fa-solid fa-star"></i> 活動剪影管理
                </a> --}}
            </li>
            @if (auth()->user()?->power == 1)
                <li class="nav-item">
                    {{-- <a class="nav-link {{ request()->is('admin/admin*') ? 'active' : '' }}"
                        href="{{ route('admin.admin') }}">
                        <i class="fa-solid fa-users"></i> 帳號管理
                    </a> --}}
                </li>
            @endif
        </ul>
    </div>
</nav>
