@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">客戶資料管理</h1>
    <a href="{{ route('backend.customer.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增客戶
    </a>
</div>

{{-- 搜尋列 + 標籤篩選 --}}
<form method="GET" action="{{ route('backend.customer.index') }}" class="mb-3 d-flex flex-wrap gap-2 align-items-center">
    <div class="input-group" style="max-width: 360px;">
        <input type="text" name="q" class="form-control" placeholder="搜尋姓名、手機、Email"
            value="{{ request('q') }}">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

    <select name="tag_id" class="form-select" style="max-width: 220px;">
        <option value="">全部標籤</option>
        @foreach ($tagCategories as $category)
            @if ($category->tags->isNotEmpty())
                <optgroup label="{{ $category->name }}">
                    @foreach ($category->tags as $tag)
                        <option value="{{ $tag->id }}"
                            {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endif
        @endforeach
    </select>

    <button class="btn btn-outline-secondary" type="submit">篩選</button>

    @if(request('q') || request('tag_id'))
        <a href="{{ route('backend.customer.index') }}" class="btn btn-outline-danger">清除</a>
    @endif
</form>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>姓名</th>
                <th>性別</th>
                <th>手機</th>
                <th>Email</th>
                <th>來源</th>
                <th>狀態</th>
                <th>標籤</th>
                <th>建立日期</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>
                        @switch($customer->gender)
                            @case('M') 男 @break
                            @case('F') 女 @break
                            @default  -
                        @endswitch
                    </td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>
                        @switch($customer->source)
                            @case('walk_in')   現場 @break
                            @case('referral')  介紹 @break
                            @case('online')    線上 @break
                            @default           其他
                        @endswitch
                    </td>
                    <td>
                        @if($customer->is_active)
                            <span class="badge bg-success">啟用</span>
                        @else
                            <span class="badge bg-secondary">停用</span>
                        @endif
                    </td>
                    <td>
                        @foreach ($customer->tags as $tag)
                            <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('backend.customer.show', $customer->id) }}"
                            class="btn btn-sm btn-info">檔案</a>
                        <a href="{{ route('backend.customer.edit', $customer->id) }}"
                            class="btn btn-sm btn-primary">編輯</a>
                        <form action="{{ route('backend.customer.destroy', $customer->id) }}"
                            method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm('確定刪除客戶「{{ $customer->name }}」？')">刪除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        @if(request('q'))
                            找不到符合「{{ request('q') }}」的客戶
                        @elseif(request('tag_id'))
                            找不到含有此標籤的客戶
                        @else
                            尚無客戶資料
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
