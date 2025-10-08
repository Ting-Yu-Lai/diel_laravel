<header class="navbar navbar-expand-md navbar-light bg-light sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('images/icons/logo.webp') }}" alt="logo" class="logo-img">
            <span class="ms-2 h5 mb-0">晝夜</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        首頁
                    </a>
                    <ul class="dropdown-menu" style="min-width: 150px;">
                        <li><a class="dropdown-item" href="#">回最上方</a></li>
                        <li><a class="dropdown-item" href="#gallery">今日甜點</a></li>
                        <li><a class="dropdown-item" href="#menu">菜單</a></li>
                        <li><a class="dropdown-item" href="#highlights">活動剪影</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" onclick="undeveloped()"; href="#">商城</a></li>
                <li class="nav-item"><a class="nav-link" onclick="undeveloped()"; href="#">線上預約</a></li>
                <li class="nav-item"><a class="nav-link" onclick="undeveloped()"; href="#">會員登入</a></li>
                <li class="nav-item"><a class="nav-link" onclick="undeveloped()"; href="#">購物車</a></li>
            </ul>
        </div>
    </div>
</header>
<script>
    function undeveloped(){
        alert("功能開發中");
    }
</script>