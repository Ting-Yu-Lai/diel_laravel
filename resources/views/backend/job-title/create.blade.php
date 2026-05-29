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
            required maxlength="50" autofocus placeholder="例：院長醫師、美容護理師、醫美顧問">
    </div>
    <div class="mb-3">
        <label class="form-label">療程角色</label>
        <select name="treatment_role" class="form-select">
            <option value="">— 無療程角色（不參與療程指派）—</option>
            <option value="doctor"     {{ old('treatment_role') === 'doctor'     ? 'selected' : '' }}>醫師（doctor）</option>
            <option value="nurse"      {{ old('treatment_role') === 'nurse'      ? 'selected' : '' }}>護理師（nurse）</option>
            <option value="consultant" {{ old('treatment_role') === 'consultant' ? 'selected' : '' }}>諮詢師（consultant）</option>
        </select>
        <div class="form-text text-muted">設定後，此職稱員工可在療程紀錄中被指派為對應角色</div>
    </div>
    <button type="submit" class="btn btn-success">
        <i class="fa-solid fa-floppy-disk"></i> 儲存
    </button>
    <a href="{{ route('backend.job-title.index') }}" class="btn btn-secondary ms-2">取消</a>
</form>
@endsection
