@extends('admin.layouts.app')

@section('content')
@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <h2>編輯輪播圖</h2>

    <form action="{{ route('admin.carousel.update', $carousel->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">標題</label>
            <input type="text" name="title" id="title" class="form-control"
                value="{{ old('title', $carousel->title) }}" placeholder="輸入圖片標題">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">圖片上傳</label>
            <input type="file" name="image" id="image" class="form-control">
            @if ($carousel->image_url)
                <img src="{{ asset($carousel->image_url) }}" alt="{{ $carousel->title }}" width="150" class="mt-2">
            @endif
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">連結</label>
            <input type="text" name="link" id="link" class="form-control"
                value="{{ old('link', $carousel->link) }}" placeholder="/product/1">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                {{ $carousel->is_active ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">啟用</label>
        </div>

        <button type="submit" class="btn btn-success">更新</button>
        <a href="{{ route('admin.carousel.index') }}" class="btn btn-secondary">返回</a>
    </form>
@endsection
