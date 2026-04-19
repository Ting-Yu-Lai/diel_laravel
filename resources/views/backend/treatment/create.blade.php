@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">新增療程項目</h1>
    <a href="{{ route('backend.treatment.index') }}" class="btn btn-secondary">返回</a>
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

<form action="{{ route('backend.treatment.store') }}" method="POST" style="max-width: 500px;">
    @csrf

    <div class="mb-3">
        <label class="form-label">療程分類 <span class="text-danger">*</span></label>
        <select name="treatment_category_id" class="form-select" required>
            <option value="">請選擇分類</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('treatment_category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">療程名稱 <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
            required maxlength="50" autofocus>
    </div>

    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">啟用</label>
        </div>
    </div>

    <button type="submit" class="btn btn-success">
        <i class="fa-solid fa-floppy-disk"></i> 儲存
    </button>
    <a href="{{ route('backend.treatment.index') }}" class="btn btn-secondary ms-2">取消</a>
</form>
@endsection
