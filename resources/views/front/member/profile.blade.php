@extends('layouts.member_panel')

@section('title', '個人資料 — DielBeauty')

@section('content')

<div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
    <h1 class="h2 mb-0">個人資料</h1>
</div>

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 基本資訊（唯讀）--}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold bg-white border-bottom">
        <i class="fa-solid fa-circle-info text-primary me-2"></i>基本資訊
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6">
                <div class="text-muted small mb-1">姓名</div>
                <div class="fw-semibold">{{ $member->full_name }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted small mb-1">備註</div>
                <div class="small text-muted">如需修改姓名請聯絡診所</div>
            </div>
        </div>
    </div>
</div>

{{-- 可編輯聯絡資訊 --}}
<div class="card shadow-sm">
    <div class="card-header fw-semibold bg-white border-bottom">
        <i class="fa-solid fa-pen-to-square text-success me-2"></i>聯絡資訊
    </div>
    <div class="card-body">
        <form action="{{ route('member.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">電子郵件 <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $member->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="form-label fw-semibold">手機號碼 <span class="text-danger">*</span></label>
                <input type="tel" name="phone" id="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $member->phone) }}"
                       placeholder="0912345678" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">儲存</button>
                <a href="{{ route('member.dashboard') }}" class="btn btn-secondary px-4">返回</a>
            </div>
        </form>
    </div>
</div>

@endsection
