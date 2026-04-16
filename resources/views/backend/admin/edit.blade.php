@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">編輯管理者帳號</h1>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('backend.admin.update', $admin->admin_id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="username" class="form-label">帳號</label>
        <input type="text" name="username" id="username" class="form-control"
            value="{{ old('username', $admin->username) }}" required>
    </div>

    <div class="mb-3">
        <label for="full_name" class="form-label">姓名</label>
        <input type="text" name="full_name" id="full_name" class="form-control"
            value="{{ old('full_name', $admin->full_name) }}">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">新密碼（留空不更改）</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>

    <div class="mb-3">
        <label for="power" class="form-label">權限</label>
        <select name="power" id="power" class="form-select">
            <option value="0" {{ $admin->power == 0 ? 'selected' : '' }}>一般管理員</option>
            <option value="1" {{ $admin->power == 1 ? 'selected' : '' }}>店長</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">更新</button>
    <a href="{{ route('backend.admin.index') }}" class="btn btn-secondary">返回</a>
</form>
@endsection
