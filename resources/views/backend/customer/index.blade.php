@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">客戶資料管理</h1>
    <a href="{{ route('backend.customer.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增客戶
    </a>
</div>

{{-- 搜尋列 + 標籤篩選 --}}
<form method="GET" action="{{ route('backend.customer.index') }}" class="mb-3 d-flex flex-wrap gap-2 align-items-center">
    <div class="input-group" style="max-width: 360px;">
        <input type="text" name="q" class="form-control" placeholder="搜尋姓名、手機、Email"
            value="{{ request('q') }}">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

    <select name="tag_id" class="form-select" style="max-width: 220px;">
        <option value="">全部標籤</option>
        @foreach ($tagCategories as $category)
            @if ($category->tags->isNotEmpty())
                <optgroup label="{{ $category->name }}">
                    @foreach ($category->tags as $tag)
                        <option value="{{ $tag->id }}"
                            {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endif
        @endforeach
    </select>

    <button class="btn btn-outline-secondary" type="submit">篩選</button>

    @if(request('q') || request('tag_id'))
        <a href="{{ route('backend.customer.index') }}" class="btn btn-outline-danger">清除</a>
    @endif
</form>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>姓名</th>
                <th>性別</th>
                <th>手機</th>
                <th>Email</th>
                <th>來源</th>
                <th>狀態</th>
                <th>標籤</th>
                <th>建立日期</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>
                        @switch($customer->gender)
                            @case('M') 男 @break
                            @case('F') 女 @break
                            @default  -
                        @endswitch
                    </td>
                    <td>{{ $customer->formatted_phone }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>
                        @switch($customer->source)
                            @case('walk_in')   現場 @break
                            @case('referral')  介紹 @break
                            @case('online')    線上 @break
                            @default           其他
                        @endswitch
                    </td>
                    <td>
                        @if($customer->is_active)
                            <span class="badge bg-success">啟用</span>
                        @else
                            <span class="badge bg-secondary">停用</span>
                        @endif
                    </td>
                    <td>
                        @foreach ($customer->tags as $tag)
                            <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('backend.customer.show', $customer->id) }}"
                            class="btn btn-sm btn-info">檔案</a>
                        <a href="{{ route('backend.customer.edit', $customer->id) }}"
                            class="btn btn-sm btn-primary">編輯</a>
                        @if(session('power') == 1)
                        <button type="button" class="btn btn-sm btn-danger"
                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                            data-id="{{ $customer->id }}"
                            data-name="{{ $customer->name }}">刪除</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        @if(request('q'))
                            找不到符合「{{ request('q') }}」的客戶
                        @elseif(request('tag_id'))
                            找不到含有此標籤的客戶
                        @else
                            尚無客戶資料
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{-- 刪除確認 Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>刪除客戶
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-3">確定要刪除客戶 <strong id="deleteCustomerName"></strong>？此操作無法復原。</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">刪除原因 <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3"
                            placeholder="請說明刪除原因（必填）" required maxlength="500"></textarea>
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
    const btn  = e.relatedTarget;
    const id   = btn.dataset.id;
    const name = btn.dataset.name;
    document.getElementById('deleteCustomerName').textContent = '「' + name + '」';
    document.getElementById('deleteForm').action =
        '{{ url('backend/customer') }}/' + id;
    this.querySelector('textarea[name="reason"]').value = '';
});
</script>
@endsection
