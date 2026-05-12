
@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">兌換申請管理</h1>
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

@if ($requests->isEmpty())
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="fa-solid fa-inbox fa-2x mb-3 d-block" style="opacity:.35;"></i>
            目前沒有待審核的兌換申請。
        </div>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>申請時間</th>
                    <th>會員</th>
                    <th>申請療程</th>
                    <th class="text-end">所需點數</th>
                    <th class="text-end">會員目前餘額</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $req)
                    <tr>
                        <td class="text-muted small">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $req->member->full_name ?? '—' }}</td>
                        <td>{{ $req->treatment->name ?? '—' }}</td>
                        <td class="text-end fw-semibold">{{ number_format($req->points_cost) }}</td>
                        <td class="text-end {{ $req->member->points_balance >= $req->points_cost ? 'text-success' : 'text-danger' }}">
                            {{ number_format($req->member->points_balance ?? 0) }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                {{-- 核准 --}}
                                <form action="{{ route('backend.redemption-request.approve', $req->id) }}" method="POST"
                                    onsubmit="return confirm('確定核准此兌換申請？點數將立即扣除。')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">核准</button>
                                </form>

                                {{-- 拒絕 --}}
                                <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                    拒絕
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- 拒絕 Modal --}}
                    <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <form action="{{ route('backend.redemption-request.reject', $req->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">拒絕兌換申請</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <span class="fw-semibold">{{ $req->member->full_name ?? '—' }}</span>
                                            申請兌換：{{ $req->treatment->name ?? '—' }}
                                        </div>
                                        <div>
                                            <label class="form-label small fw-semibold">拒絕原因（選填）</label>
                                            <textarea name="admin_note" class="form-control form-control-sm" rows="3"
                                                maxlength="500" placeholder="輸入拒絕原因，會顯示給會員"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">取消</button>
                                        <button type="submit" class="btn btn-danger btn-sm">確認拒絕</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
