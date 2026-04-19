<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('backend') ? 'active' : '' }}"
                    href="{{ route('backend.index') }}">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('backend/carousel*') ? 'active' : '' }}"
                    href="{{ route('backend.carousel.index') }}">
                    <i class="fa-solid fa-image"></i> 輪播圖管理
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('backend/customer*') ? 'active' : '' }}"
                    href="{{ route('backend.customer.index') }}">
                    <i class="fa-solid fa-user-injured"></i> 客戶管理
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('backend/staff*') ? 'active' : '' }}"
                    href="{{ route('backend.staff.index') }}">
                    <i class="fa-solid fa-user-tie"></i> 工作人員管理
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('backend/tag*') ? 'active' : '' }}"
                    href="{{ route('backend.tag-category.index') }}">
                    <i class="fa-solid fa-tags"></i> 標籤管理
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('backend/treatment*') ? 'active' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#treatmentMenu" role="button"
                    aria-expanded="{{ request()->is('backend/treatment*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-syringe"></i> 療程管理
                </a>
                <div class="collapse {{ request()->is('backend/treatment*') ? 'show' : '' }}" id="treatmentMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('backend/treatment-category*') ? 'active' : '' }}"
                                href="{{ route('backend.treatment-category.index') }}">
                                療程分類
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('backend/treatment') || (request()->is('backend/treatment/*') && !request()->is('backend/treatment-category*')) ? 'active' : '' }}"
                                href="{{ route('backend.treatment.index') }}">
                                療程項目
                            </a>
                        </li>
                    </ul>
                </div>
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
            @if (Session::get('power') == 1)
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('backend/job-title*') ? 'active' : '' }}"
                        href="{{ route('backend.job-title.index') }}">
                        <i class="fa-solid fa-briefcase"></i> 職稱管理
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('backend/admin*') ? 'active' : '' }}"
                        href="{{ route('backend.admin.index') }}">
                        <i class="fa-solid fa-users"></i> 帳號管理
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
