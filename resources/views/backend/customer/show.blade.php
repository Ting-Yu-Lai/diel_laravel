@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        客戶檔案 — {{ $customer->name }}
        @if($customer->is_active)
            <span class="badge bg-success fs-6 ms-2">啟用</span>
        @else
            <span class="badge bg-secondary fs-6 ms-2">停用</span>
        @endif
    </h1>
    <div>
        <a href="{{ route('backend.customer.edit', $customer->id) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i> 編輯
        </a>
        <a href="{{ route('backend.customer.index') }}" class="btn btn-secondary ms-2">返回列表</a>
    </div>
</div>

<div class="row g-4">

    {{-- 基本資料 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-circle-user me-1"></i> 基本資料
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">姓名</dt>
                    <dd class="col-sm-8">{{ $customer->name }}</dd>

                    <dt class="col-sm-4">性別</dt>
                    <dd class="col-sm-8">
                        @switch($customer->gender)
                            @case('M') 男 @break
                            @case('F') 女 @break
                            @case('other') 其他 @break
                            @default —
                        @endswitch
                    </dd>

                    <dt class="col-sm-4">出生日期</dt>
                    <dd class="col-sm-8">{{ $customer->birth_date?->format('Y-m-d') ?? '—' }}</dd>

                    <dt class="col-sm-4">血型</dt>
                    <dd class="col-sm-8">{{ $customer->blood_type === 'unknown' ? '不明' : ($customer->blood_type ?? '—') }}</dd>

                    <dt class="col-sm-4">身分證字號</dt>
                    <dd class="col-sm-8">{{ $customer->id_number ?? '—' }}</dd>

                    <dt class="col-sm-4">職業</dt>
                    <dd class="col-sm-8">{{ $customer->occupation ?? '—' }}</dd>

                    <dt class="col-sm-4">來源</dt>
                    <dd class="col-sm-8">
                        @switch($customer->source)
                            @case('walk_in')  現場 @break
                            @case('referral') 介紹 @break
                            @case('online')   線上 @break
                            @default          其他
                        @endswitch
                    </dd>

                    <dt class="col-sm-4">建立日期</dt>
                    <dd class="col-sm-8">{{ $customer->created_at->format('Y-m-d') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 聯絡資訊 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-address-book me-1"></i> 聯絡資訊
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">手機</dt>
                    <dd class="col-sm-8">{{ $customer->phone }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $customer->email ?? '—' }}</dd>

                    <dt class="col-sm-4">地址</dt>
                    <dd class="col-sm-8">{{ $customer->address ?? '—' }}</dd>

                    <dt class="col-sm-4 mt-3">緊急聯絡人</dt>
                    <dd class="col-sm-8 mt-3">{{ $customer->emergency_contact ?? '—' }}</dd>

                    <dt class="col-sm-4">緊急電話</dt>
                    <dd class="col-sm-8">{{ $customer->emergency_phone ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 醫療資訊 --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-notes-medical me-1"></i> 醫療資訊
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">過敏史</dt>
                    <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $customer->allergies ?? '—' }}</dd>

                    <dt class="col-sm-4 mt-2">病史</dt>
                    <dd class="col-sm-8 mt-2" style="white-space: pre-wrap;">{{ $customer->medical_history ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 備註 --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-clipboard me-1"></i> 備註
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $customer->notes ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- 標籤 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-tags me-1"></i> 標籤
            </div>
            <div class="card-body">
                @if ($customer->tags->isEmpty())
                    <span class="text-muted">尚未指派標籤</span>
                @else
                    @php
                        $tagsByCategory = $customer->tags->groupBy(fn($tag) => $tag->category->name ?? '未分類');
                    @endphp
                    @foreach ($tagsByCategory as $categoryName => $tags)
                        <div class="mb-2">
                            <small class="text-muted me-2">{{ $categoryName }}</small>
                            @foreach ($tags as $tag)
                                <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
