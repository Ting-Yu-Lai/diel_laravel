@extends('layouts.login')

@section('title', '會員註冊')

@section('content')
<div class="login-box">
    <h2 class="text-center mb-4">會員註冊</h2>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('member.register') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="full_name" class="form-label">用戶名</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">帳號</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">電子郵件</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">密碼</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">確認密碼</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-login">註冊</button>
        </div>
        <div class="mt-3 text-center">
            <a href="{{ route('member.loginForm') }}">已經有帳號？前往登入</a>
        </div>
    </form>
</div>
@endsection
