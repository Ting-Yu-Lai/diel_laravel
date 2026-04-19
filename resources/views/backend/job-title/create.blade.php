@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">新增職稱</h1>
    <a href="{{ route('backend.job-title.index') }}" class="btn btn-secondary">返回</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form action="{{ route('backend.job-title.store') }}" method="POST" style="max-width: 400px;">
    @csrf
    <div class="mb-3">
        <label class="form-label">職稱名稱 <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
            required maxlength="50" autofocus placeholder="例：醫師、護理師、諮詢師">
    </div>
    <button type="submit" class="btn btn-success">
        <i class="fa-solid fa-floppy-disk"></i> 儲存
    </button>
    <a href="{{ route('backend.job-title.index') }}" class="btn btn-secondary ms-2">取消</a>
</form>
@endsection
