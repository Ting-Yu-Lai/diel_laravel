@extends('layouts.member_panel')

@section('title', '術後追蹤 — DielBeauty')

@section('content')

<div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
    <h1 class="h2 mb-0">術後追蹤</h1>
    <span class="text-muted small">術後照護與追蹤紀錄</span>
</div>

@php
    $statusLabel = ['ongoing' => '追蹤中', 'completed' => '已完成', 'abnormal' => '異常'];
    $statusBadge = ['ongoing' => 'bg-warning text-dark', 'completed' => 'bg-success', 'abnormal' => 'bg-danger'];
@endphp

@php $hasAny = false; @endphp

@foreach ($records as $record)
    @foreach ($record->items as $item)
        @if ($item->followUp)
            @php
                $followUp = $item->followUp;
                $hasAny   = true;

                $allPhotos    = $followUp->logs->flatMap(fn($log) => $log->photos);
                $beforePhotos = $allPhotos->filter(fn($p) => $p->category === '術前');
                $afterPhotos  = $allPhotos->filter(fn($p) => $p->category === '術後');
                $hasComparison = $beforePhotos->isNotEmpty() || $afterPhotos->isNotEmpty();
            @endphp

            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa-solid fa-heart text-danger me-2"></i>
                        {{ $item->treatment->name ?? '—' }}
                        @if ($item->body_part)
                            <span class="text-muted fw-normal ms-2 small">{{ $item->body_part }}</span>
                        @endif
                        <span class="text-muted fw-normal ms-3 small">{{ $record->record_date->format('Y-m-d') }}</span>
                    </div>
                    <span class="badge {{ $statusBadge[$followUp->status] ?? 'bg-secondary' }}">
                        {{ $statusLabel[$followUp->status] ?? $followUp->status }}
                    </span>
                </div>

                <div class="card-body">

                    {{-- 備註 --}}
                    @if ($followUp->notes)
                        <div class="alert alert-light border-start border-4 border-primary mb-4 py-2">
                            <small class="text-muted d-block mb-1">備註</small>
                            <div>{{ $followUp->notes }}</div>
                        </div>
                    @endif

                    {{-- 術前術後對比 --}}
                    @if ($hasComparison)
                        <h6 class="fw-semibold text-muted mb-3" style="font-size:.75rem; letter-spacing:.1em; text-transform:uppercase;">
                            術前術後對比
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="fw-semibold small mb-2">術前</div>
                                @forelse ($beforePhotos as $photo)
                                    <img src="{{ $photo->photo_url }}" alt="術前"
                                         class="d-block mb-2 rounded"
                                         style="width:100%; max-height:200px; object-fit:cover; border:1px solid #dee2e6;">
                                @empty
                                    <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted small"
                                         style="height:110px; border:1px dashed #dee2e6;">
                                        無術前照片
                                    </div>
                                @endforelse
                            </div>
                            <div class="col-6">
                                <div class="fw-semibold small mb-2">術後</div>
                                @forelse ($afterPhotos as $photo)
                                    <img src="{{ $photo->photo_url }}" alt="術後"
                                         class="d-block mb-2 rounded"
                                         style="width:100%; max-height:200px; object-fit:cover; border:1px solid #dee2e6;">
                                @empty
                                    <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted small"
                                         style="height:110px; border:1px dashed #dee2e6;">
                                        無術後照片
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <hr class="mb-4">
                    @endif

                    {{-- 追蹤時間軸 --}}
                    @if ($followUp->logs->isNotEmpty())
                        <h6 class="fw-semibold text-muted mb-3" style="font-size:.75rem; letter-spacing:.1em; text-transform:uppercase;">
                            追蹤時間軸
                        </h6>

                        @foreach ($followUp->logs->sortBy('day_number') as $log)
                            <div class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-pill">第 {{ $log->day_number }} 天</span>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-2 small">{{ $log->content }}</p>

                                    @if ($log->photos->isNotEmpty())
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm py-1 px-3 mb-1"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#log-photos-{{ $log->id }}"
                                                aria-expanded="false">
                                            <i class="fa-regular fa-image me-1"></i>
                                            查看照片（{{ $log->photos->count() }} 張）
                                            <i class="fa-solid fa-chevron-down ms-1" style="font-size:.65rem;"></i>
                                        </button>
                                        <div class="collapse" id="log-photos-{{ $log->id }}">
                                            <div class="d-flex flex-wrap gap-2 mt-2 p-2 bg-light rounded border">
                                                @foreach ($log->photos as $photo)
                                                    <img src="{{ $photo->photo_url }}"
                                                         alt="{{ $photo->category }}"
                                                         class="rounded"
                                                         style="height:110px; width:110px; object-fit:cover; border:1px solid #dee2e6;">
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">尚無追蹤紀錄。</p>
                    @endif

                </div>
            </div>
        @endif
    @endforeach
@endforeach

@if (!$hasAny)
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="fa-solid fa-heart fa-2x mb-3 d-block" style="opacity:.35;"></i>
            目前尚無術後追蹤紀錄。
        </div>
    </div>
@endif

@endsection
