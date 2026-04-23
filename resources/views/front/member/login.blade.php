@extends('layouts.login')

@section('title', '會員登入 — DielBeauty')

@section('content')
<div class="login-box">
    <h2 class="text-center mb-1" style="font-family:'Cormorant Garamond',serif; font-weight:400; letter-spacing:0.1em; font-size:1.6rem;">
        會員登入
    </h2>
    <p class="text-center mb-4" style="font-size:0.72rem; letter-spacing:0.18em; color:rgba(239,230,221,0.4);">
        MEMBER LOGIN
    </p>

    @if ($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:0.85rem;">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('member.login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="login" class="form-label">Email 或手機號碼</label>
            <input type="text" name="login" id="login"
                   class="form-control"
                   value="{{ old('login') }}"
                   placeholder="example@mail.com 或 0912345678"
                   required autofocus>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">密碼</label>
            <div class="input-group">
                <input type="password" name="password" id="password"
                       class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                    <i class="fa fa-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-login">登入</button>
        </div>
    </form>

    <div class="text-center" style="font-size:0.82rem;">
        <a href="{{ route('member.registerForm') }}"
           style="color:rgba(214,179,112,0.7); letter-spacing:0.04em;">
            還沒有帳號？立即註冊
        </a>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script>
(function () {
    const input  = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    const icon   = document.getElementById('toggleIcon');
    toggle.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
    });
})();
</script>
@endsection
