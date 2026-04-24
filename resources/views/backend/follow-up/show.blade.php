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
    $current      = $statusMap[$followUp->status] ?? $statusMap['ongoing'];
    $lineMember   = $record->customer->member ?? null;
    $canLineRemind = $lineMember?->line_user_id && $followUp->status === 'ongoing';
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">術後追蹤</h1>
    <div class="d-flex gap-2">
        @if ($canLineRemind)
            <button type="button" id="line-remind-btn"
                class="btn btn-success"
                data-member-id="{{ $lineMember->id }}">
                <i class="fa-brands fa-line"></i> 發送 LINE 提醒
            </button>
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

<div class="row g-4">

    {{-- 基本資訊與狀態編輯 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">追蹤資訊</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="text-muted" style="width:110px;">客戶</th>
                        <td>
                            <a href="{{ route('backend.customer.show', $record->customer_id) }}">
                                {{ $record->customer->name ?? '—' }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">療程項目</th>
                        <td>{{ $item->treatment->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">來診日期</th>
                        <td>{{ $record->record_date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">目前狀態</th>
                        <td>
                            <span class="badge {{ $current['badge'] }}">{{ $current['label'] }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- 狀態與備註編輯 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">編輯追蹤狀態</div>
            <div class="card-body">
                <form action="{{ route('backend.follow-up.update', $followUp->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">追蹤狀態 <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="ongoing"   {{ $followUp->status === 'ongoing'   ? 'selected' : '' }}>進行中</option>
                            <option value="completed" {{ $followUp->status === 'completed' ? 'selected' : '' }}>完成</option>
                            <option value="abnormal"  {{ $followUp->status === 'abnormal'  ? 'selected' : '' }}>異常</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">整體追蹤備註</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                            rows="4" maxlength="5000"
                            placeholder="填寫整體追蹤備註（選填）">{{ old('notes', $followUp->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">儲存</button>
                </form>
            </div>
        </div>
    </div>

    {{-- 新增追蹤紀錄 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">新增追蹤紀錄</div>
            <div class="card-body">
                <form action="{{ route('backend.follow-up.log.store', $followUp->id) }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">第幾天 <span class="text-danger">*</span></label>
                            <input type="number" name="day_number" min="1"
                                class="form-control @error('day_number') is-invalid @enderror"
                                value="{{ old('day_number') }}" placeholder="例：1">
                            @error('day_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">追蹤內容 <span class="text-danger">*</span></label>
                            <textarea name="content" rows="2"
                                class="form-control @error('content') is-invalid @enderror"
                                maxlength="5000"
                                placeholder="填寫此次追蹤狀況">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa-solid fa-plus"></i> 新增
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 追蹤時間軸 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">追蹤時間軸</div>
            <div class="card-body p-0">
                @forelse ($followUp->logs as $log)
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0">
                                <span class="badge bg-secondary me-2">第 {{ $log->day_number }} 天</span>
                                <small class="text-muted fw-normal">{{ $log->created_at->format('Y-m-d H:i') }}</small>
                            </h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('backend.follow-up.log.edit', [$followUp->id, $log->id]) }}"
                                   class="btn btn-sm btn-outline-primary">編輯</a>
                                @if (Session::get('power') == 1)
                                    <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteLogModal"
                                        data-log-id="{{ $log->id }}"
                                        data-follow-up-id="{{ $followUp->id }}"
                                        data-day="{{ $log->day_number }}">
                                        刪除
                                    </button>
                                @endif
                            </div>
                        </div>

                        <p class="mb-3" style="white-space:pre-wrap;">{{ $log->content }}</p>

                        {{-- 照片區塊 --}}
                        @if ($log->photos->isNotEmpty())
                            @php $catMap = ['before' => '術前', 'after' => '術後', 'recovery' => '恢復']; @endphp
                            <div class="row g-2 mb-3">
                                @foreach ($log->photos as $photo)
                                    <div class="col-auto">
                                        <div class="card" style="width:140px;">
                                            <a href="{{ $photo->photo_url }}" target="_blank">
                                                <img src="{{ $photo->photo_url }}" class="card-img-top"
                                                    style="height:100px;object-fit:cover;"
                                                    alt="{{ $catMap[$photo->category] ?? $photo->category }}">
                                            </a>
                                            <div class="card-body p-1 text-center">
                                                <span class="badge bg-light text-dark small">
                                                    {{ $catMap[$photo->category] ?? $photo->category }}
                                                </span>
                                            </div>
                                            <div class="card-footer p-1 text-center">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm w-100"
                                                    data-delete-photo-url="{{ route('backend.follow-up.photo.destroy', [$log->id, $photo->id]) }}">
                                                    刪除
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- 上傳照片（支援多張） --}}
                        <form action="{{ route('backend.follow-up.photo.store', $log->id) }}"
                            method="POST" enctype="multipart/form-data"
                            class="border rounded p-2 bg-light">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold mb-1">
                                        上傳照片 <span class="text-muted fw-normal">（可複選多張）</span>
                                    </label>
                                    <input type="file" name="photos[]" accept="image/*" multiple
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">照片類別</label>
                                    <select name="category" class="form-select form-select-sm">
                                        <option value="before">術前 (Before)</option>
                                        <option value="after">術後 (After)</option>
                                        <option value="recovery">恢復期 (Recovery)</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-upload"></i> 上傳
                                    </button>
                                </div>
                            </div>
                            <div class="small text-muted mt-1">
                                同一類別可一次選取多張，不同類別請分批上傳，每張上限 4MB
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">尚無追蹤紀錄，請使用上方表單新增</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- 前後對比（Before / After） --}}
@php
    $beforePhotos   = $followUp->logs->flatMap(fn($l) => $l->photos->where('category', 'before'));
    $afterPhotos    = $followUp->logs->flatMap(fn($l) => $l->photos->where('category', 'after'));
    $recoveryPhotos = $followUp->logs->flatMap(fn($l) => $l->photos->where('category', 'recovery'));
@endphp

@if ($beforePhotos->isNotEmpty() || $afterPhotos->isNotEmpty() || $recoveryPhotos->isNotEmpty())
<div class="row g-4 mt-0">
    @if ($beforePhotos->isNotEmpty() || $afterPhotos->isNotEmpty())
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">術前 / 術後對比</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="fw-semibold text-muted small mb-2">術前 (Before)</div>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse ($beforePhotos as $p)
                                <a href="{{ $p->photo_url }}" target="_blank">
                                    <img src="{{ $p->photo_url }}" style="height:120px;object-fit:cover;border-radius:4px;" alt="術前">
                                </a>
                            @empty
                                <span class="text-muted small">無術前照片</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold text-muted small mb-2">術後 (After)</div>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse ($afterPhotos as $p)
                                <a href="{{ $p->photo_url }}" target="_blank">
                                    <img src="{{ $p->photo_url }}" style="height:120px;object-fit:cover;border-radius:4px;" alt="術後">
                                </a>
                            @empty
                                <span class="text-muted small">無術後照片</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($recoveryPhotos->isNotEmpty())
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">恢復時間軸照片</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($recoveryPhotos as $p)
                        @php
                            $day = $followUp->logs->firstWhere('id', $p->follow_up_log_id)?->day_number ?? '?';
                        @endphp
                        <div class="text-center">
                            <a href="{{ $p->photo_url }}" target="_blank">
                                <img src="{{ $p->photo_url }}" style="height:100px;object-fit:cover;border-radius:4px;" alt="恢復">
                            </a>
                            <div class="small text-muted mt-1">第 {{ $day }} 天</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

{{-- 共享：刪除照片表單（JS 動態更新 action） --}}
<form id="delete-photo-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- Modal：刪除追蹤紀錄 --}}
@if (Session::get('power') == 1)
<div class="modal fade" id="deleteLogModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-log-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">刪除追蹤紀錄</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>確定要刪除 <strong id="delete-log-day"></strong> 的追蹤紀錄？此操作不可復原，相關照片也將一併刪除。</p>
                    <div class="mb-3">
                        <label class="form-label">刪除原因 <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3"
                            placeholder="請填寫刪除原因（必填）" required maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">確認刪除</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('deleteLogModal').addEventListener('show.bs.modal', function (e) {
    const btn        = e.relatedTarget;
    const logId      = btn.getAttribute('data-log-id');
    const followUpId = btn.getAttribute('data-follow-up-id');
    const day        = btn.getAttribute('data-day');

    document.getElementById('delete-log-day').textContent = '第 ' + day + ' 天';
    document.getElementById('delete-log-form').action =
        `/backend/follow-up/${followUpId}/log/${logId}`;
    document.querySelector('#deleteLogModal textarea[name="reason"]').value = '';
});
</script>
@endif

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
    var remindBtn = document.getElementById('line-remind-btn');
    if (!remindBtn) return;

    remindBtn.addEventListener('click', function () {
        var memberId = remindBtn.getAttribute('data-member-id');
        remindBtn.disabled = true;
        remindBtn.textContent = '發送中…';

        fetch('/api/line/remind', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ member_id: parseInt(memberId) }),
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            alert(data.message);
        })
        .catch(function () {
            alert('發送失敗，請稍後再試。');
        })
        .finally(function () {
            remindBtn.disabled = false;
            remindBtn.innerHTML = '<i class="fa-brands fa-line"></i> 發送 LINE 提醒';
        });
    });
})();
</script>

@endsection
