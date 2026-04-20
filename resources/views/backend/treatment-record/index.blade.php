@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">療程紀錄管理</h1>
    <a href="{{ route('backend.treatment-record.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增療程紀錄
    </a>
</div>

{{-- 篩選列 --}}
<form id="treatment-record-filter-form"
      method="GET"
      action="{{ route('backend.treatment-record.index') }}"
      class="mb-3 d-flex flex-wrap gap-2 align-items-end">

    <div style="min-width:200px;">
        <label class="form-label mb-1 small">客戶</label>
        <div class="position-relative">
            <input type="text"
                   id="filter-customer-search-text"
                   class="form-control form-control-sm"
                   placeholder="輸入姓名或電話搜尋..."
                   autocomplete="off"
                   value="{{ $filteredCustomer?->name ?? '' }}">
            <input type="hidden"
                   id="filter-customer-id-hidden"
                   name="customer_id"
                   value="{{ request('customer_id') }}">
            <div id="filter-customer-search-results"
                 class="list-group position-absolute w-100 shadow-sm"
                 style="z-index:1050; display:none; max-height:220px; overflow-y:auto; top:100%;"></div>
        </div>
    </div>

    <div>
        <label class="form-label mb-1 small">日期從</label>
        <input type="date" name="date_from" class="form-control form-control-sm"
            value="{{ request('date_from') }}" style="width:140px;">
    </div>

    <div>
        <label class="form-label mb-1 small">日期至</label>
        <input type="date" name="date_to" class="form-control form-control-sm"
            value="{{ request('date_to') }}" style="width:140px;">
    </div>

    <div>
        <label class="form-label mb-1 small">人員</label>
        <select name="staff_id" class="form-select form-select-sm" style="min-width:120px;">
            <option value="">全部人員</option>
            @foreach ($allStaff as $s)
                <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>
                    {{ $s->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="d-flex gap-1 align-items-end">
        <button class="btn btn-sm btn-outline-secondary" type="submit">篩選</button>
        @if (request()->hasAny(['customer_id', 'date_from', 'date_to', 'staff_id']))
            <a href="{{ route('backend.treatment-record.index') }}" class="btn btn-sm btn-outline-danger">清除</a>
        @endif
    </div>
</form>

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

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>客戶</th>
                <th>來診日期</th>
                <th>當月</th>
                <th>總金額</th>
                <th>毛利</th>
                <th>項目數</th>
                <th>身份</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->customer->name ?? '—' }}</td>
                    <td>{{ $record->record_date->format('Y-m-d') }}</td>
                    <td>{{ $record->record_month }}</td>
                    <td>NT$ {{ number_format($record->total_amount) }}</td>
                    <td class="{{ $record->total_profit < 0 ? 'text-danger' : '' }}">
                        NT$ {{ number_format($record->total_profit) }}
                    </td>
                    <td>{{ $record->item_count }}</td>
                    <td>
                        @if ($record->is_new_customer)
                            <span class="badge bg-primary">新客</span>
                        @else
                            <span class="badge bg-secondary">回診</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('backend.treatment-record.show', $record->id) }}"
                            class="btn btn-sm btn-info text-white">查看</a>
                        <a href="{{ route('backend.treatment-record.edit', $record->id) }}"
                            class="btn btn-sm btn-primary">編輯</a>
                        @if (Session::get('power') == 1)
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-id="{{ $record->id }}"
                                data-label="{{ $record->customer->name ?? '' }} {{ $record->record_date->format('Y-m-d') }}">
                                刪除
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">尚無療程紀錄</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $records->links() }}

@if (Session::get('power') == 1)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">刪除療程紀錄</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>確定要刪除 <strong id="deleteLabel"></strong> 的療程紀錄？此操作不可復原。</p>
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
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('deleteLabel').textContent = btn.getAttribute('data-label');
    document.getElementById('deleteForm').action = `/backend/treatment-record/${btn.getAttribute('data-id')}`;
    document.querySelector('#deleteModal textarea[name="reason"]').value = '';
});
</script>
@endif

<script>
(function () {
    const searchUrl    = '{{ route('backend.customer.searchJson') }}';
    const textInput    = document.getElementById('filter-customer-search-text');
    const hiddenInput  = document.getElementById('filter-customer-id-hidden');
    const resultList   = document.getElementById('filter-customer-search-results');
    let debounceTimer;

    function closeResults() {
        resultList.innerHTML = '';
        resultList.style.display = 'none';
    }

    textInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        hiddenInput.value = '';
        const keyword = this.value.trim();
        if (keyword.length < 1) { closeResults(); return; }

        debounceTimer = setTimeout(() => {
            fetch(searchUrl + '?q=' + encodeURIComponent(keyword))
                .then(r => r.json())
                .then(customers => {
                    resultList.innerHTML = '';
                    if (!customers.length) {
                        resultList.innerHTML = '<div class="list-group-item text-muted small py-2">查無符合客戶</div>';
                    } else {
                        customers.forEach(customer => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'list-group-item list-group-item-action py-2 small';
                            btn.textContent = customer.name + (customer.phone ? '　' + customer.phone : '');
                            btn.addEventListener('click', () => {
                                textInput.value   = customer.name;
                                hiddenInput.value = customer.id;
                                closeResults();
                            });
                            resultList.appendChild(btn);
                        });
                    }
                    resultList.style.display = 'block';
                });
        }, 280);
    });

    // 點擊外部關閉搜尋結果
    document.addEventListener('click', e => {
        if (!textInput.contains(e.target) && !resultList.contains(e.target)) {
            closeResults();
        }
    });

    // 清除文字時同步清除 hidden customer_id
    textInput.addEventListener('change', function () {
        if (!this.value.trim()) {
            hiddenInput.value = '';
        }
    });
})();
</script>
@endsection
