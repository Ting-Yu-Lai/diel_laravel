@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">編輯標籤 — {{ $tag->name }}</h1>
    <a href="{{ route('backend.tag-category.index') }}" class="btn btn-secondary">返回</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('backend.tag.update', $tag->id) }}" method="POST" style="max-width: 400px;">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">標籤名稱 <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
            value="{{ old('name', $tag->name) }}" required maxlength="50" autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">所屬分類 <span class="text-danger">*</span></label>
        <select name="tag_category_id" class="form-select" required>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ $tag->tag_category_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-success">
        <i class="fa-solid fa-floppy-disk"></i> 儲存變更
    </button>
    <a href="{{ route('backend.tag-category.index') }}" class="btn btn-secondary ms-2">取消</a>
</form>
@endsection
