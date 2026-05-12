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
    $canLineRemind = (bool) $lineMember?->line_user_id;
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

{{-- 術前 / 術後對比 --}}
<style>
.img-compare {
    position: relative;
    overflow: hidden;
    border-radius: .375rem;
    border: 1px solid #dee2e6;
    cursor: ew-resize;
    touch-action: none;
    user-select: none;
    background: #f0f0f0;
}
.img-compare .compare-base,
.img-compare .compare-after {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    pointer-events: none;
}
.img-compare .compare-after { clip-path: inset(0 50% 0 0); }
.img-compare .compare-spacer {
    display: block;
    width: 100%;
    max-height: 360px;
    object-fit: cover;
    visibility: hidden;
}
.img-compare .compare-handle {
    position: absolute;
    top: 0; left: 50%;
    transform: translateX(-50%);
    width: 3px; height: 100%;
    background: rgba(255,255,255,.9);
    pointer-events: none;
}
.img-compare .compare-handle-btn {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; color: #444;
}
.img-compare .compare-label {
    position: absolute;
    top: 10px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
    letter-spacing: .05em;
    pointer-events: none;
}
.compare-label-before { right: 10px; background: rgba(0,0,0,.5);       color: #fff; }
.compare-label-after  { left:  10px; background: rgba(13,110,253,.85); color: #fff; }
</style>

<div class="card mb-4">
    <div class="card-header fw-semibold d-flex align-items-center gap-2">
        <span class="badge bg-dark">對比</span> 術前 / 術後照片對比
    </div>
    <div class="card-body">

        {{-- 術前照片 --}}
        <p class="fw-semibold text-primary mb-2"><span class="badge bg-primary me-1">術前</span> Pre-Op</p>
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
            method="POST" enctype="multipart/form-data" class="border rounded p-2 bg-light mb-4">
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

        {{-- 術後照片 --}}
        <p class="fw-semibold text-success mb-2"><span class="badge bg-success me-1">術後</span> Post-Op</p>
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
        <form action="{{ route('backend.follow-up.photo.store', $followUp->id) }}"
            method="POST" enctype="multipart/form-data" class="border rounded p-2 bg-light mb-4">
            @csrf
            <input type="hidden" name="category" value="after">
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label small fw-semibold mb-1">上傳術後照片 <span class="text-muted fw-normal">（可多張）</span></label>
                    <input type="file" name="photos[]" accept="image/*" multiple class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="fa-solid fa-upload"></i> 上傳
                    </button>
                </div>
            </div>
        </form>

        {{-- 拖曳對比滑桿（僅當術前術後皆有照片時顯示） --}}
        @if ($followUp->preOpPhotos->isNotEmpty() && $followUp->postOpPhotos->isNotEmpty())
            @php
                $beforeUrl = $followUp->preOpPhotos->first()->photo_url;
                $afterUrl  = $followUp->postOpPhotos->first()->photo_url;
            @endphp
            <hr class="my-3">
            <p class="fw-semibold mb-2"><span class="badge bg-dark me-1">對比</span> 術前 / 術後滑桿對比</p>
            <div class="img-compare mb-1" id="cmp-{{ $followUp->id }}" style="max-width:600px;">
                <img src="{{ $beforeUrl }}" class="compare-base" alt="術前">
                <img src="{{ $afterUrl }}"  class="compare-after" alt="術後">
                <img src="{{ $beforeUrl }}" class="compare-spacer" alt="" aria-hidden="true">
                <span class="compare-label compare-label-after">術後</span>
                <span class="compare-label compare-label-before">術前</span>
                <div class="compare-handle">
                    <div class="compare-handle-btn">◀▶</div>
                </div>
            </div>
            <p class="text-muted small"><i class="fa-solid fa-left-right me-1"></i>拖曳中間滑桿比較術前術後</p>
        @endif

    </div>
</div>

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

(function () {
    document.querySelectorAll('.img-compare').forEach(function (cmp) {
        var after  = cmp.querySelector('.compare-after');
        var handle = cmp.querySelector('.compare-handle');
        var active = false;

        function update(clientX) {
            var rect = cmp.getBoundingClientRect();
            var pct  = Math.min(100, Math.max(0, (clientX - rect.left) / rect.width * 100));
            after.style.clipPath  = 'inset(0 ' + (100 - pct) + '% 0 0)';
            handle.style.left     = pct + '%';
        }

        cmp.addEventListener('mousedown',  function (e) { active = true; update(e.clientX); });
        cmp.addEventListener('touchstart', function (e) { active = true; update(e.touches[0].clientX); }, { passive: true });
        document.addEventListener('mousemove',  function (e) { if (active) update(e.clientX); });
        document.addEventListener('touchmove',  function (e) { if (active) update(e.touches[0].clientX); }, { passive: true });
        document.addEventListener('mouseup',    function () { active = false; });
        document.addEventListener('touchend',   function () { active = false; });
    });
}());
</script>

@endsection
