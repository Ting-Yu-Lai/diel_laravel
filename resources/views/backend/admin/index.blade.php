@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">管理者帳號管理</h1>
    <a href="{{ route('backend.admin.create') }}" class="btn btn-success">新增管理者</a>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>帳號</th>
            <th>姓名</th>
            <th>權限</th>
            <th>最後登入</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($admins as $admin)
            <tr>
                <td>{{ $admin->id }}</td>
                <td>{{ $admin->username }}</td>
                <td>{{ $admin->full_name ?? '-' }}</td>
                <td>{{ $admin->power == 1 ? '超級管理者' : '一般管理員' }}</td>
                <td>{{ $admin->last_login_at ?? '-' }}</td>
                <td>
                    <a href="{{ route('backend.admin.edit', $admin->id) }}" class="btn btn-sm btn-primary">編輯</a>
                    <form action="{{ route('backend.admin.destroy', $admin->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                            onclick="return confirm('確定刪除？')">刪除</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
