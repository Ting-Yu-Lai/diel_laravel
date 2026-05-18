@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">管理者帳號管理</h1>
    @if (session('power') >= 2)
        <a href="{{ route('backend.admin.create') }}" class="btn btn-success">
            <i class="fa fa-plus me-1"></i>新增管理者
        </a>
    @endif
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>帳號</th>
                <th>姓名</th>
                <th>Email</th>
                <th>權限</th>
                <th>最後登入</th>
                @if (session('power') >= 2)
                    <th>操作</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td>{{ $admin->username }}</td>
                    <td>{{ $admin->full_name ?? '-' }}</td>
                    <td>{{ $admin->email ?? '-' }}</td>
                    <td>
                        @if ($admin->power == 2)
                            <span class="badge bg-danger">超級管理員</span>
                        @elseif ($admin->power == 1)
                            <span class="badge bg-warning text-dark">店長</span>
                        @else
                            <span class="badge bg-secondary">一般管理員</span>
                        @endif
                    </td>
                    <td>
                        {{ $admin->last_login_at
                            ? \Carbon\Carbon::parse($admin->last_login_at)->format('Y/m/d H:i')
                            : '-' }}
                    </td>
                    @if (session('power') >= 2)
                        <td>
                            <a href="{{ route('backend.admin.edit', $admin->id) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fa fa-edit me-1"></i>編輯
                            </a>
                            @if ($admin->id !== session('admin_id'))
                                <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $admin->id }}"
                                        data-name="{{ $admin->username }}">
                                    <i class="fa fa-trash me-1"></i>刪除
                                </button>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- 刪除確認 Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">確認刪除</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                確定要刪除管理者帳號「<strong id="modalAdminName"></strong>」嗎？此操作無法還原。
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">確認刪除</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn    = e.relatedTarget;
    const id     = btn.dataset.id;
    const name   = btn.dataset.name;
    document.getElementById('modalAdminName').textContent = name;
    document.getElementById('deleteForm').action = '/backend/admin/' + id;
});
</script>
@endsection
