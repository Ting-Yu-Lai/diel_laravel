@extends('layouts.member_panel')

@section('title', '療程紀錄 — DielBeauty')

@section('content')

<div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
    <h1 class="h2 mb-0">療程紀錄</h1>
    <span class="text-muted small">共 {{ $records->count() }} 筆紀錄</span>
</div>

@if ($records->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="fa-solid fa-clipboard-list fa-2x mb-3 d-block" style="opacity:.35;"></i>
            目前尚無療程紀錄。
        </div>
    </div>
@else
    @foreach ($records as $record)
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold bg-white border-bottom d-flex justify-content-between align-items-center">
                <span>
                    <i class="fa-regular fa-calendar me-2 text-secondary"></i>
                    {{ $record->record_date->format('Y-m-d') }}
                </span>
                <span class="badge bg-secondary">共 {{ $record->item_count }} 項</span>
            </div>

            @if ($record->items->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>療程名稱</th>
                                <th>施作部位</th>
                                <th>劑量</th>
                                <th>備註</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($record->items as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->treatment->name ?? '—' }}</td>
                                    <td>{{ $item->body_part ?: '—' }}</td>
                                    <td>{{ $item->dose ?: '—' }}</td>
                                    <td class="text-muted small">{{ $item->notes ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($record->notes)
                <div class="card-footer text-muted small">
                    <i class="fa-solid fa-note-sticky me-1"></i>{{ $record->notes }}
                </div>
            @endif
        </div>
    @endforeach
@endif

@endsection
