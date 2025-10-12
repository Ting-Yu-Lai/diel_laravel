@extends('layouts.login')

@section('title', '會員登入')

@section('content')
<div class="login-box">
    <h2 class="text-center mb-4">會員登入</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('member.login') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="login" class="form-label">帳號或 Email</label>
            <input type="text" name="login" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">密碼</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-login">登入</button>
        </div>
    </form>
</div>
@endsection
