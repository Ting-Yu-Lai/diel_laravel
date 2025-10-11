
@extends('admin.layouts.app')

@section('content')

{{-- @if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}
<h2>新增輪播圖</h2>

<form action="{{ route('admin.carousel.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="title" class="form-label">標題</label>
        <input type="text" name="title" id="title" class="form-control" placeholder="輸入圖片標題">
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">圖片上傳</label>
        <input type="file" name="image" id="image" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="link" class="form-label">連結</label>
        <input type="text" name="link" id="link" class="form-control" placeholder="/product/1">
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
        <label class="form-check-label" for="is_active">啟用</label>
    </div>

    <button type="submit" class="btn btn-success">新增</button>
    <a href="{{ route('admin.carousel.index') }}" class="btn btn-secondary">返回</a>
</form>
@endsection
