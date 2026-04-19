@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">職稱管理</h1>
    <a href="{{ route('backend.job-title.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增職稱
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>職稱名稱</th>
                <th>工作人員數</th>
                <th>建立時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jobTitles as $jobTitle)
                <tr>
                    <td>{{ $jobTitle->id }}</td>
                    <td>{{ $jobTitle->name }}</td>
                    <td>{{ $jobTitle->staff()->count() }}</td>
                    <td>{{ $jobTitle->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('backend.job-title.edit', $jobTitle->id) }}"
                            class="btn btn-sm btn-primary">編輯</a>
                        <form action="{{ route('backend.job-title.destroy', $jobTitle->id) }}"
                            method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm('確定刪除職稱「{{ $jobTitle->name }}」？')">刪除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">尚無職稱資料</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
