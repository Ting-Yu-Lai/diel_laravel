@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">工作人員管理</h1>
    <a href="{{ route('backend.staff.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增工作人員
    </a>
</div>

{{-- 搜尋 + 職稱篩選 --}}
<form method="GET" action="{{ route('backend.staff.index') }}" class="mb-3 d-flex flex-wrap gap-2 align-items-center">
    <div class="input-group" style="max-width: 300px;">
        <input type="text" name="q" class="form-control" placeholder="搜尋姓名"
            value="{{ request('q') }}">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

    <select name="job_title_id" class="form-select" style="max-width: 200px;">
        <option value="">全部職稱</option>
        @foreach ($jobTitles as $jt)
            <option value="{{ $jt->id }}" {{ request('job_title_id') == $jt->id ? 'selected' : '' }}>
                {{ $jt->name }}
            </option>
        @endforeach
    </select>

    <button class="btn btn-outline-secondary" type="submit">篩選</button>

    @if(request('q') || request('job_title_id'))
        <a href="{{ route('backend.staff.index') }}" class="btn btn-outline-danger">清除</a>
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
                <th>姓名</th>
                <th>職稱</th>
                <th>性別</th>
                <th>手機</th>
                <th>狀態</th>
                <th>到職日</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($staffList as $member)
                <tr>
                    <td>{{ $member->id }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->jobTitle->name ?? '—' }}</td>
                    <td>
                        @switch($member->gender)
                            @case('M') 男 @break
                            @case('F') 女 @break
                            @case('other') 其他 @break
                            @default —
                        @endswitch
                    </td>
                    <td>{{ $member->phone }}</td>
                    <td>
                        @if($member->is_active)
                            <span class="badge bg-success">在職</span>
                        @else
                            <span class="badge bg-secondary">停用</span>
                        @endif
                    </td>
                    <td>{{ $member->hire_date?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('backend.staff.show', $member->id) }}"
                            class="btn btn-sm btn-info">檔案</a>
                        <a href="{{ route('backend.staff.edit', $member->id) }}"
                            class="btn btn-sm btn-primary">編輯</a>
                        @if (Session::get('power') == 1)
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-id="{{ $member->id }}"
                                data-name="{{ $member->name }}">刪除</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        @if(request('q'))
                            找不到符合「{{ request('q') }}」的工作人員
                        @elseif(request('job_title_id'))
                            找不到此職稱的工作人員
                        @else
                            尚無工作人員資料
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (Session::get('power') == 1)
{{-- 刪除確認 Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">刪除工作人員</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>確定要刪除工作人員 <strong id="deleteName"></strong>？此操作不可復原。</p>
                    <div class="mb-3">
                        <label class="form-label">異動原因 <span class="text-danger">*</span></label>
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
    document.getElementById('deleteForm').action = `/backend/staff/${id}`;
    document.querySelector('#deleteModal textarea[name="reason"]').value = '';
});
</script>
@endif
@endsection
