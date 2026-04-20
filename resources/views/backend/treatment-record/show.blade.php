@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">療程紀錄詳情</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('backend.treatment-record.edit', $record->id) }}" class="btn btn-primary">編輯</a>
        <a href="{{ route('backend.treatment-record.index') }}" class="btn btn-secondary">返回列表</a>
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

    {{-- 基本資訊 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">基本資訊</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="text-muted" style="width:120px;">客戶</th>
                        <td>
                            <a href="{{ route('backend.customer.show', $record->customer_id) }}">
                                {{ $record->customer->name ?? '—' }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">來診日期</th>
                        <td>{{ $record->record_date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">當月</th>
                        <td>{{ $record->record_month }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">身份</th>
                        <td>
                            @if ($record->is_new_customer)
                                <span class="badge bg-primary">新客</span>
                            @else
                                <span class="badge bg-secondary">回診</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">上次來診</th>
                        <td>{{ $record->last_visit_date?->format('Y-m-d') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">備註</th>
                        <td>{{ $record->notes ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- 金額摘要 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">金額摘要</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="text-muted" style="width:120px;">療程項目數</th>
                        <td>{{ $record->item_count }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">總金額</th>
                        <td>NT$ {{ number_format($record->total_amount) }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">總成本</th>
                        <td>NT$ {{ number_format($record->total_cost) }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">毛利</th>
                        <td class="{{ $record->total_profit < 0 ? 'text-danger' : 'text-success' }} fw-semibold">
                            NT$ {{ number_format($record->total_profit) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- 人員 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">執行人員</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="fw-semibold mb-1 text-muted small">醫師</div>
                        @forelse ($record->doctors as $s)
                            <span class="badge bg-danger me-1">{{ $s->name }}</span>
                        @empty
                            <span class="text-muted small">—</span>
                        @endforelse
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold mb-1 text-muted small">護理師</div>
                        @forelse ($record->nurses as $s)
                            <span class="badge bg-info me-1">{{ $s->name }}</span>
                        @empty
                            <span class="text-muted small">—</span>
                        @endforelse
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold mb-1 text-muted small">諮詢師</div>
                        @forelse ($record->consultants as $s)
                            <span class="badge bg-warning text-dark me-1">{{ $s->name }}</span>
                        @empty
                            <span class="text-muted small">—</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 療程明細 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">療程明細</span>
                <a href="{{ route('backend.treatment-record-item.create', $record->id) }}"
                   class="btn btn-sm btn-success">
                    <i class="fa-solid fa-plus"></i> 新增項目
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>療程名稱</th>
                            <th class="text-end">售價</th>
                            <th class="text-end">成本</th>
                            <th>負責醫師</th>
                            <th>備註</th>
                            <th style="width:120px;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($record->items as $item)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $item->treatment->name ?? '—' }}</td>
                                <td class="text-end">NT$ {{ number_format($item->price) }}</td>
                                <td class="text-end">NT$ {{ number_format($item->cost) }}</td>
                                <td>{{ $item->staff?->name ?? '—' }}</td>
                                <td class="text-muted small">{{ $item->notes ?: '—' }}</td>
                                <td>
                                    <a href="{{ route('backend.treatment-record-item.edit', [$record->id, $item->id]) }}"
                                       class="btn btn-sm btn-primary">編輯</a>
                                    @if (Session::get('power') == 1)
                                        <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteItemModal"
                                            data-item-id="{{ $item->id }}"
                                            data-item-name="{{ $item->treatment->name ?? '' }}">
                                            刪除
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">尚無療程明細</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- 刪除療程紀錄按鈕 --}}
@if (Session::get('power') == 1)
<div class="mt-4">
    <button class="btn btn-danger"
        data-bs-toggle="modal" data-bs-target="#deleteRecordModal">
        <i class="fa-solid fa-trash"></i> 刪除此紀錄
    </button>
</div>

{{-- Modal：刪除療程明細項目 --}}
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-item-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">刪除療程明細</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>確定要刪除明細項目：<strong id="delete-item-name"></strong>？此操作不可復原。</p>
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

{{-- Modal：刪除整筆療程紀錄 --}}
<div class="modal fade" id="deleteRecordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.treatment-record.destroy', $record->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">刪除療程紀錄</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>確定要刪除 <strong>{{ $record->customer->name ?? '' }}</strong>
                        於 <strong>{{ $record->record_date->format('Y-m-d') }}</strong> 的療程紀錄？此操作不可復原。</p>
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
document.getElementById('deleteItemModal').addEventListener('show.bs.modal', function (e) {
    const btn      = e.relatedTarget;
    const itemId   = btn.getAttribute('data-item-id');
    const itemName = btn.getAttribute('data-item-name');

    document.getElementById('delete-item-name').textContent = itemName;
    document.getElementById('delete-item-form').action =
        `/backend/treatment-record/{{ $record->id }}/item/${itemId}`;
    document.querySelector('#deleteItemModal textarea[name="reason"]').value = '';
});
</script>
@endif
@endsection
