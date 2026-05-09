@extends('backend.layouts.app')

@section('content')
@php
    $item   = $followUp->treatmentRecordItem;
    $record = $item->treatmentRecord;
    $statusMap = [
        'ongoing'   => ['label' => '進行中', 'badge' => 'bg-warning text-dark'],
        'completed' => ['label' => '完成',   'badge' => 'bg-success'],
        'abnormal'  => ['label' => '異常',   'badge' => 'bg-danger'],
    ];
    $current     = $statusMap[$followUp->status] ?? $statusMap['ongoing'];
    $lineMember  = $record->customer->member ?? null;
    $canLineRemind = $lineMember?->line_user_id && $followUp->status === 'ongoing';
    $baseline    = $followUp->created_at->startOfDay();
    $hasPreOp    = $followUp->preOpPhotos->isNotEmpty();
    $isCompleted = $followUp->status === 'completed';
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">術後追蹤</h1>
    <div class="d-flex gap-2">
        @if ($canLineRemind)
            <form action="{{ route('backend.follow-up.remind', $followUp->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fa-brands fa-line"></i> 發送 LINE 提醒
                </button>
            </form>
        @endif
        <a href="{{ route('backend.treatment-record.show', $record->id) }}" class="btn btn-secondary">
            返回療程紀錄
        </a>
    </div>
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

{{-- 基本資訊 + 狀態編輯 --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">追蹤資訊</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="text-muted" style="width:110px;">客戶</th>
                        <td><a href="{{ route('backend.customer.show', $record->customer_id) }}">{{ $record->customer->name ?? '—' }}</a></td>
                    </tr>
                    <tr>
                        <th class="text-muted">療程項目</th>
                        <td>{{ $item->treatment->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">療程日期</th>
                        <td>{{ $record->record_date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">追蹤基準日</th>
                        <td>{{ $followUp->created_at->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">目前狀態</th>
                        <td><span class="badge {{ $current['badge'] }}">{{ $current['label'] }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">編輯追蹤狀態</div>
            <div class="card-body">
                <form action="{{ route('backend.follow-up.update', $followUp->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">追蹤狀態 <span class="text-danger">*</span></label>
                        <select name="status" class="form-select">
                            <option value="ongoing"   {{ $followUp->status === 'ongoing'   ? 'selected' : '' }}>進行中</option>
                            <option value="completed" {{ $followUp->status === 'completed' ? 'selected' : '' }}>完成</option>
                            <option value="abnormal"  {{ $followUp->status === 'abnormal'  ? 'selected' : '' }}>異常</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">整體追蹤備註</label>
                        <textarea name="notes" class="form-control" rows="3" maxlength="5000"
                            placeholder="填寫整體追蹤備註（選填）">{{ old('notes', $followUp->notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">儲存</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ① 術前照片 --}}
<div class="card mb-4">
    <div class="card-header fw-semibold d-flex align-items-center gap-2">
        <span class="badge bg-primary">術前</span> Pre-Operation 照片
    </div>      
    <div class="card-body">
        @if ($followUp->preOpPhotos->isNotEmpty())
            <div class="row g-2 mb-3">
                @foreach ($followUp->preOpPhotos as $photo)
                    <div class="col-auto">
                        <div class="card" style="width:140px;">
                            <a href="{{ $photo->photo_url }}" target="_blank">
                                <img src="{{ $photo->photo_url }}" class="card-img-top"
                                    style="height:100px;object-fit:cover;" alt="術前">
                            </a>
                            <div class="card-footer p-1 text-center">
                                <button type="button" class="btn btn-danger btn-sm w-100"
                                    data-delete-photo-url="{{ route('backend.follow-up.photo.destroy', $photo->id) }}">
                                    刪除
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small mb-3">尚未上傳術前照片</p>
        @endif

        <form action="{{ route('backend.follow-up.photo.store', $followUp->id) }}"
            method="POST" enctype="multipart/form-data" class="border rounded p-2 bg-light">
            @csrf
            <input type="hidden" name="category" value="before">
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label small fw-semibold mb-1">上傳術前照片 <span class="text-muted fw-normal">（可多張）</span></label>
                    <input type="file" name="photos[]" accept="image/*" multiple class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-upload"></i> 上傳
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ② 恢復期時間軸 --}}
<div class="card mb-4">
    <div class="card-header fw-semibold d-flex align-items-center gap-2">
        <span class="badge bg-warning text-dark">恢復期</span> Recovery Timeline
    </div>
    <div class="card-body">
        @if (!$hasPreOp)
            <div class="alert alert-warning py-2 mb-3">
                <i class="fa-solid fa-triangle-exclamation"></i> 建議先上傳術前照片再開始恢復期追蹤
            </div>
        @endif

        @forelse ($followUp->logs as $log)
            @php
                $logDate = $baseline->copy()->addDays($log->day_number);
            @endphp
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <span class="badge bg-secondary me-1">第 {{ $log->day_number }} 天</span>
                        <small class="text-muted">{{ $logDate->format('Y-m-d') }}（共 {{ $log->photos->count() }} 張）</small>
                    </h6>
                </div>
                <div class="row g-2">
                    @foreach ($log->photos as $photo)
                        <div class="col-auto">
                            <div class="card" style="width:140px;">
                                <a href="{{ $photo->photo_url }}" target="_blank">
                                    <img src="{{ $photo->photo_url }}" class="card-img-top"
                                        style="height:100px;object-fit:cover;" alt="恢復期">
                                </a>
                                <div class="card-footer p-1 text-center">
                                    <button type="button" class="btn btn-danger btn-sm w-100"
                                        data-delete-photo-url="{{ route('backend.follow-up.photo.destroy', $photo->id) }}">
                                        刪除
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-muted small mb-3">尚無恢復期照片</p>
        @endforelse

        @if (!$isCompleted)
            @php
                $todayDay = (int) $baseline->diffInDays(now()->startOfDay()) + 1;
            @endphp
            <form action="{{ route('backend.follow-up.photo.store', $followUp->id) }}"
                method="POST" enctype="multipart/form-data" class="border rounded p-2 bg-light">
                @csrf
                <input type="hidden" name="category" value="recovery">
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold mb-1">
                            上傳今日恢復期照片
                            <span class="text-muted fw-normal">（第 {{ $todayDay }} 天・可多張）</span>
                        </label>
                        <input type="file" name="photos[]" accept="image/*" multiple class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-outline-warning btn-sm">
                            <i class="fa-solid fa-upload"></i> 上傳
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- ③ 術後照片 --}}
<div class="card mb-4">
    <div class="card-header fw-semibold d-flex align-items-center gap-2">
        <span class="badge bg-success">術後</span> Post-Operation 照片
        @if ($isCompleted)
            <span class="badge bg-success ms-1">追蹤完成</span>
        @endif
    </div>
    <div class="card-body">
        @if (!$hasPreOp && !$isCompleted)
            <div class="alert alert-warning py-2 mb-3">
                <i class="fa-solid fa-triangle-exclamation"></i> 建議先上傳術前照片再上傳術後照片
            </div>
        @endif

        @if ($followUp->postOpPhotos->isNotEmpty())
            <div class="row g-2 mb-3">
                @foreach ($followUp->postOpPhotos as $photo)
                    <div class="col-auto">
                        <div class="card" style="width:140px;">
                            <a href="{{ $photo->photo_url }}" target="_blank">
                                <img src="{{ $photo->photo_url }}" class="card-img-top"
                                    style="height:100px;object-fit:cover;" alt="術後">
                            </a>
                            <div class="card-footer p-1 text-center">
                                <button type="button" class="btn btn-danger btn-sm w-100"
                                    data-delete-photo-url="{{ route('backend.follow-up.photo.destroy', $photo->id) }}">
                                    刪除
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small mb-3">尚未上傳術後照片</p>
        @endif

        @if (!$isCompleted)
            <form action="{{ route('backend.follow-up.photo.store', $followUp->id) }}"
                method="POST" enctype="multipart/form-data" class="border rounded p-2 bg-light">
                @csrf
                <input type="hidden" name="category" value="after">
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold mb-1">
                            上傳術後照片 <span class="text-muted fw-normal">（上傳後追蹤狀態自動設為完成）</span>
                        </label>
                        <input type="file" name="photos[]" accept="image/*" multiple class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-upload"></i> 上傳
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- ④ 術前術後對比 --}}
@if ($followUp->preOpPhotos->isNotEmpty() && $followUp->postOpPhotos->isNotEmpty())
<div class="card mb-4">
    <div class="card-header fw-semibold d-flex align-items-center gap-2">
        <span class="badge bg-dark">對比</span> 術前 / 術後對比
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="fw-semibold text-primary mb-2"><span class="badge bg-primary me-1">術前</span> Pre-Op</p>
                <div class="row g-2">
                    @foreach ($followUp->preOpPhotos as $photo)
                        <div class="col-auto">
                            <a href="{{ $photo->photo_url }}" target="_blank">
                                <img src="{{ $photo->photo_url }}"
                                    style="width:140px;height:140px;object-fit:cover;border-radius:4px;" alt="術前">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <p class="fw-semibold text-success mb-2"><span class="badge bg-success me-1">術後</span> Post-Op</p>
                <div class="row g-2">
                    @foreach ($followUp->postOpPhotos as $photo)
                        <div class="col-auto">
                            <a href="{{ $photo->photo_url }}" target="_blank">
                                <img src="{{ $photo->photo_url }}"
                                    style="width:140px;height:140px;object-fit:cover;border-radius:4px;" alt="術後">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 共享：刪除照片表單 --}}
<form id="delete-photo-form" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<script>
document.querySelectorAll('[data-delete-photo-url]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        if (!confirm('確定要刪除此照片？')) return;
        var form = document.getElementById('delete-photo-form');
        form.action = btn.getAttribute('data-delete-photo-url');
        form.submit();
    });
});

</script>

@endsection
