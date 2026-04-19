@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">標籤管理</h1>
    <a href="{{ route('backend.tag-category.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增分類
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

@if ($categories->isEmpty())
    <p class="text-muted">尚無標籤分類，請點擊「新增分類」開始建立。</p>
@else
    <div class="row g-4">
        @foreach ($categories as $category)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $category->name }}</span>
                        <div class="d-flex gap-1">
                            <a href="{{ route('backend.tag-category.edit', $category->id) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('backend.tag-category.destroy', $category->id) }}"
                                method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('確定刪除分類「{{ $category->name }}」？')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($category->tags->isEmpty())
                            <p class="text-muted small mb-2">尚無標籤</p>
                        @else
                            <div class="mb-3 d-flex flex-wrap gap-2">
                                @foreach ($category->tags as $tag)
                                    <span class="badge bg-secondary d-flex align-items-center gap-1 fs-6 fw-normal px-2 py-1">
                                        {{ $tag->name }}
                                        <a href="{{ route('backend.tag.edit', $tag->id) }}"
                                            class="text-white ms-1" title="編輯">
                                            <i class="fa-solid fa-pen fa-xs"></i>
                                        </a>
                                        <form action="{{ route('backend.tag.destroy', $tag->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn p-0 text-white border-0" title="刪除"
                                                onclick="return confirm('確定刪除標籤「{{ $tag->name }}」？')">
                                                <i class="fa-solid fa-xmark fa-xs"></i>
                                            </button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- 新增標籤 inline form --}}
                        <form action="{{ route('backend.tag.store', $category->id) }}" method="POST"
                            class="d-flex gap-2 mt-2">
                            @csrf
                            <input type="text" name="name" class="form-control form-control-sm"
                                placeholder="新增標籤名稱" required maxlength="50">
                            <button type="submit" class="btn btn-sm btn-outline-success text-nowrap">
                                <i class="fa-solid fa-plus"></i> 新增
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
