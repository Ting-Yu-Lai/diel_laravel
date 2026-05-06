@extends('layouts.member_panel')

@section('title', '術後追蹤 — DielBeauty')

@section('content')

<style>
/* ── 垂直時間軸 ── */
.recovery-timeline { position: relative; padding-left: 2.75rem; }
.recovery-timeline::before {
    content: '';
    position: absolute;
    left: 0.85rem;
    top: 0.5rem;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #0d6efd55, #dee2e600);
    border-radius: 1px;
}
.timeline-entry { position: relative; margin-bottom: 1.75rem; }
.timeline-dot {
    position: absolute;
    left: -2.75rem;
    top: 0.1rem;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 50%;
    background: #0d6efd;
    color: #fff;
    font-size: 0.55rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
    box-shadow: 0 0 0 3px #fff, 0 0 0 5px #0d6efd33;
}

/* ── 照片橫向滑動條 ── */
.photo-strip {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.375rem;
    scrollbar-width: thin;
    scrollbar-color: #dee2e6 transparent;
}
.photo-strip::-webkit-scrollbar { height: 3px; }
.photo-strip::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 2px; }
.photo-strip-item {
    flex-shrink: 0;
    width: 5.5rem;
    height: 5.5rem;
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
}
.photo-strip-item:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(0,0,0,.15); }
.photo-strip-item img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* ── 對比照片 ── */
.comparison-img-wrap {
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    margin-bottom: 0.5rem;
}
.comparison-img-wrap:hover { transform: scale(1.02); box-shadow: 0 6px 16px rgba(0,0,0,.12); }
.comparison-img-wrap img { width: 100%; max-height: 220px; object-fit: cover; display: block; }

/* ── 區塊標題 ── */
.section-label {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 0.875rem;
}
</style>

<div class="pb-2 mb-4 border-bottom">
    <h1 class="h3 mb-0 fw-bold">術後追蹤</h1>
    <small class="text-muted">術後照護與個人恢復紀錄</small>
</div>

@php
    $statusLabel = ['ongoing' => '追蹤中', 'completed' => '已完成', 'abnormal' => '異常'];
    $statusBadge = ['ongoing' => 'bg-warning text-dark', 'completed' => 'bg-success', 'abnormal' => 'bg-danger'];
    $weekDays    = ['日', '一', '二', '三', '四', '五', '六'];
@endphp

@php $hasAny = false; @endphp

@foreach ($records as $record)
    @foreach ($record->items as $item)
        @if ($item->followUp)
            @php
                $followUp      = $item->followUp;
                $hasAny        = true;
                $beforePhotos  = $followUp->preOpPhotos;
                $afterPhotos   = $followUp->postOpPhotos;
                $hasComparison = $beforePhotos->isNotEmpty() && $afterPhotos->isNotEmpty();
                $baseline      = $followUp->created_at->startOfDay();
                $todayDay      = (int) $baseline->diffInDays(now()->startOfDay());
                $sortedLogs    = $followUp->logs->sortBy('day_number');
                $totalPhotos   = $sortedLogs->sum(fn($l) => $l->photos->count());
            @endphp

            <div class="card border-0 shadow-sm mb-5">

                {{-- 卡片 Header --}}
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div class="min-w-0">
                            <div class="fw-bold mb-1">
                                <i class="fa-solid fa-heart text-danger me-2" style="font-size:.85em;"></i>{{ $item->treatment->name ?? '—' }}
                                @if ($item->body_part)
                                    <span class="text-muted fw-normal small">・{{ $item->body_part }}</span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                療程日期 {{ $record->record_date->format('Y年m月d日') }}
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge {{ $statusBadge[$followUp->status] ?? 'bg-secondary' }} d-block mb-1">
                                {{ $statusLabel[$followUp->status] ?? $followUp->status }}
                            </span>
                            @if ($followUp->status === 'ongoing')
                                <span class="text-muted small">第 {{ $todayDay }} 天</span>
                            @endif
                        </div>
                    </div>

                    @if ($sortedLogs->isNotEmpty() || $totalPhotos > 0)
                        <div class="d-flex gap-3 mt-2 pt-2 border-top">
                            <span class="text-muted small">
                                <i class="fa-regular fa-calendar-check me-1"></i>已追蹤 {{ $sortedLogs->count() }} 天
                            </span>
                            <span class="text-muted small">
                                <i class="fa-regular fa-image me-1"></i>{{ $totalPhotos }} 張恢復期照片
                            </span>
                        </div>
                    @endif
                </div>

                <div class="card-body px-4 py-4">

                    {{-- 備註 --}}
                    @if ($followUp->notes)
                        <div class="p-3 mb-4 rounded-3 bg-light border-start border-4 border-primary">
                            <small class="text-primary fw-semibold d-block mb-1">
                                <i class="fa-solid fa-clipboard-list me-1"></i>備註
                            </small>
                            <div class="small">{{ $followUp->notes }}</div>
                        </div>
                    @endif

                    {{-- 術前術後對比（兩邊都有才顯示，避免空白佔位框）--}}
                    @if ($hasComparison)
                        <div class="section-label">術前 / 術後對比</div>
                        <div class="row g-3 mb-5">
                            <div class="col-6">
                                <div class="text-muted small fw-semibold mb-2">術前</div>
                                @foreach ($beforePhotos as $photo)
                                    <div class="comparison-img-wrap photo-lightbox-trigger" data-src="{{ $photo->photo_url }}">
                                        <img src="{{ $photo->photo_url }}" alt="術前">
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-6">
                                <div class="text-muted small fw-semibold mb-2">術後</div>
                                @foreach ($afterPhotos as $photo)
                                    <div class="comparison-img-wrap photo-lightbox-trigger" data-src="{{ $photo->photo_url }}">
                                        <img src="{{ $photo->photo_url }}" alt="術後">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 恢復期時間軸 --}}
                    @if ($sortedLogs->isNotEmpty())
                        <div class="section-label">恢復期時間軸</div>
                        <div class="recovery-timeline">
                            @foreach ($sortedLogs as $log)
                                @php
                                    $logDate = $baseline->copy()->addDays($log->day_number);
                                @endphp
                                <div class="timeline-entry">
                                    <div class="timeline-dot">{{ $log->day_number }}</div>
                                    <div class="ps-1">
                                        <div class="fw-semibold small mb-1">
                                            第 {{ $log->day_number }} 天
                                            <span class="text-muted fw-normal">
                                                · {{ $logDate->format('m月d日') }}（週{{ $weekDays[$logDate->dayOfWeek] }}）
                                            </span>
                                        </div>
                                        @if ($log->content)
                                            <p class="small text-muted mb-2">{{ $log->content }}</p>
                                        @endif
                                        @if ($log->photos->isNotEmpty())
                                            <div class="photo-strip">
                                                @foreach ($log->photos as $photo)
                                                    <div class="photo-strip-item photo-lightbox-trigger"
                                                         data-src="{{ $photo->photo_url }}">
                                                        <img src="{{ $photo->photo_url }}"
                                                             alt="第{{ $log->day_number }}天">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-clock fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            <small>等待您的第一張恢復期照片</small>
                        </div>
                    @endif

                </div>
            </div>
        @endif
    @endforeach
@endforeach

@if (!$hasAny)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="fa-solid fa-heart fa-2x mb-3 d-block" style="opacity:.2; color:#dc3545;"></i>
            <div class="fw-semibold mb-1">目前尚無術後追蹤紀錄</div>
            <small>完成療程後，診所會為您建立追蹤紀錄</small>
        </div>
    </div>
@endif

{{-- Lightbox Modal --}}
<div class="modal fade" id="photo-lightbox-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="background:rgba(0,0,0,.82);">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white ms-auto"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-1 pb-4 text-center">
                <img id="photo-lightbox-img" src="" alt="照片"
                     class="img-fluid rounded" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.photo-lightbox-trigger').forEach(function (el) {
    el.addEventListener('click', function () {
        document.getElementById('photo-lightbox-img').src = el.getAttribute('data-src');
        new bootstrap.Modal(document.getElementById('photo-lightbox-modal')).show();
    });
});
</script>

@endsection
