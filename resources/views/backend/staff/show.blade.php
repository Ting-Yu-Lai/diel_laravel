@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        工作人員檔案 — {{ $staff->name }}
        @if($staff->is_active)
            <span class="badge bg-success fs-6 ms-2">在職</span>
        @else
            <span class="badge bg-secondary fs-6 ms-2">停用</span>
        @endif
    </h1>
    <div>
        <a href="{{ route('backend.staff.edit', $staff->id) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i> 編輯
        </a>
        @if (Session::get('power') == 1)
            <button class="btn btn-danger ms-2"
                data-bs-toggle="modal" data-bs-target="#deleteModal"
                data-id="{{ $staff->id }}" data-name="{{ $staff->name }}">
                <i class="fa-solid fa-trash"></i> 刪除
            </button>
        @endif
        <a href="{{ route('backend.staff.index') }}" class="btn btn-secondary ms-2">返回列表</a>
    </div>
</div>

<div class="row g-4">

    {{-- 基本資料 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-circle-user me-1"></i> 基本資料
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">姓名</dt>
                    <dd class="col-sm-8">{{ $staff->name }}</dd>

                    <dt class="col-sm-4">職稱</dt>
                    <dd class="col-sm-8">{{ $staff->jobTitle->name ?? '—' }}</dd>

                    <dt class="col-sm-4">性別</dt>
                    <dd class="col-sm-8">
                        @switch($staff->gender)
                            @case('M') 男 @break
                            @case('F') 女 @break
                            @case('other') 其他 @break
                            @default —
                        @endswitch
                    </dd>

                    <dt class="col-sm-4">到職日</dt>
                    <dd class="col-sm-8">{{ $staff->hire_date?->format('Y-m-d') ?? '—' }}</dd>

                    <dt class="col-sm-4">建立時間</dt>
                    <dd class="col-sm-8">{{ $staff->created_at->format('Y-m-d') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 聯絡資訊 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-address-book me-1"></i> 聯絡資訊
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">手機</dt>
                    <dd class="col-sm-8">{{ $staff->phone }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $staff->email ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 備註 --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-clipboard me-1"></i> 備註
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $staff->notes ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- 刪除記錄（僅 power==1 可見）--}}
    @if (Session::get('power') == 1 && $deleteLogs->isNotEmpty())
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header fw-bold text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> 異動記錄
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>異動時間</th>
                                <th>異動者</th>
                                <th>異動原因</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deleteLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->admin->full_name ?? $log->admin->username ?? '—' }}</td>
                                    <td>{{ $log->reason }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

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
                    <p>確定要刪除工作人員 <strong>{{ $staff->name }}</strong>？此操作不可復原。</p>
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
document.getElementById('deleteForm').action = '{{ route('backend.staff.destroy', $staff->id) }}';
</script>
@endif
@endsection
