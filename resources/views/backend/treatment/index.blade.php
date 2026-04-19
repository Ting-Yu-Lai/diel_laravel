@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">療程項目管理</h1>
    <a href="{{ route('backend.treatment.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增療程
    </a>
</div>

{{-- 分類篩選 --}}
<form method="GET" action="{{ route('backend.treatment.index') }}" class="mb-3 d-flex flex-wrap gap-2 align-items-center">
    <select name="category_id" class="form-select" style="max-width: 220px;">
        <option value="">全部分類</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <button class="btn btn-outline-secondary" type="submit">篩選</button>
    @if (request('category_id'))
        <a href="{{ route('backend.treatment.index') }}" class="btn btn-outline-danger">清除</a>
    @endif
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
                <th>療程名稱</th>
                <th>所屬分類</th>
                <th>狀態</th>
                <th>建立日期</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($treatments as $treatment)
                <tr>
                    <td>{{ $treatment->id }}</td>
                    <td>{{ $treatment->name }}</td>
                    <td>{{ $treatment->treatmentCategory->name ?? '—' }}</td>
                    <td>
                        @if ($treatment->is_active)
                            <span class="badge bg-success">啟用</span>
                        @else
                            <span class="badge bg-secondary">停用</span>
                        @endif
                    </td>
                    <td>{{ $treatment->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('backend.treatment.edit', $treatment->id) }}"
                            class="btn btn-sm btn-primary">編輯</a>

                        <form action="{{ route('backend.treatment.toggle', $treatment->id) }}"
                            method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $treatment->is_active ? 'btn-warning' : 'btn-outline-success' }}">
                                {{ $treatment->is_active ? '停用' : '啟用' }}
                            </button>
                        </form>

                        @if (Session::get('power') == 1)
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-id="{{ $treatment->id }}"
                                data-name="{{ $treatment->name }}">刪除</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        @if (request('category_id'))
                            此分類下尚無療程項目
                        @else
                            尚無療程項目資料
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (Session::get('power') == 1)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">刪除療程項目</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>確定要刪除療程 <strong id="deleteName"></strong>？此操作不可復原。</p>
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
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteForm').action = `/backend/treatment/${id}`;
    document.querySelector('#deleteModal textarea[name="reason"]').value = '';
});
</script>
@endif
@endsection
