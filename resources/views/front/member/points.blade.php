@extends('layouts.member_panel')

@section('title', '點數中心 — DielBeauty')

@section('content')

<div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
    <h1 class="h2 mb-0">點數中心</h1>
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

{{-- 餘額卡 --}}
<div class="card shadow-sm mb-4" style="background: linear-gradient(135deg,#f6d365,#fda085); border:none;">
    <div class="card-body text-center py-4">
        <div class="text-white small fw-semibold mb-1">目前點數餘額</div>
        <div class="display-4 fw-bold text-white">{{ number_format($member->points_balance) }}</div>
        <div class="text-white small mt-1 opacity-75">每筆療程紀錄自動累積 50 點</div>
    </div>
</div>

<div class="row g-4">
    {{-- 左：可兌換療程清單 --}}
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">
                <i class="fa-solid fa-gift me-2 text-warning"></i>可兌換療程
            </div>
            <div class="card-body p-0">
                @if ($catalog->isEmpty())
                    <div class="text-center text-muted py-5 small">
                        <i class="fa-solid fa-box-open fa-2x mb-3 d-block" style="opacity:.35;"></i>
                        目前尚無開放兌換的療程。
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($catalog as $treatment)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-semibold">{{ $treatment->name }}</div>
                                    <div class="small text-muted">{{ $treatment->treatmentCategory->name ?? '' }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                        {{ number_format($treatment->redemption_points) }} 點
                                    </span>
                                    @if ($member->points_balance >= $treatment->redemption_points)
                                        <form method="POST" action="{{ route('member.points.redeem') }}"
                                            onsubmit="return confirm('確定要申請兌換「{{ $treatment->name }}」嗎？')">
                                            @csrf
                                            <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                                            <button type="submit" class="btn btn-sm btn-warning">申請兌換</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">點數不足</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- 右：點數明細 + 我的申請 --}}
    <div class="col-lg-7">

        {{-- 我的兌換申請 --}}
        @if ($myRequests->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold bg-white">
                <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i>我的兌換申請
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>申請日期</th>
                            <th>療程</th>
                            <th class="text-end">點數</th>
                            <th class="text-center">狀態</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($myRequests as $req)
                            <tr>
                                <td class="text-muted small">{{ $req->created_at->format('Y-m-d') }}</td>
                                <td>{{ $req->treatment->name ?? '—' }}</td>
                                <td class="text-end">{{ number_format($req->points_cost) }}</td>
                                <td class="text-center">
                                    @if ($req->status === 'pending')
                                        <span class="badge bg-warning text-dark">審核中</span>
                                    @elseif ($req->status === 'approved')
                                        <span class="badge bg-success">已核准</span>
                                    @else
                                        <span class="badge bg-danger">已拒絕</span>
                                        @if ($req->admin_note)
                                            <div class="small text-muted">{{ $req->admin_note }}</div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- 點數歷史 --}}
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="fa-solid fa-list-ul me-2 text-secondary"></i>點數明細
            </div>
            @if ($logs->isEmpty())
                <div class="card-body text-center text-muted py-5 small">
                    <i class="fa-solid fa-coins fa-2x mb-3 d-block" style="opacity:.35;"></i>
                    尚無點數記錄。
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>日期</th>
                                <th class="text-center">類型</th>
                                <th class="text-end">點數</th>
                                <th class="text-end">餘額</th>
                                <th>說明</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td class="text-muted small">{{ $log->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        @if ($log->type === 'earn')
                                            <span class="badge bg-success">獲得</span>
                                        @elseif ($log->type === 'redeem')
                                            <span class="badge bg-warning text-dark">兌換</span>
                                        @else
                                            <span class="badge bg-secondary">調整</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold {{ $log->points > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $log->points > 0 ? '+' : '' }}{{ number_format($log->points) }}
                                    </td>
                                    <td class="text-end text-muted small">{{ number_format($log->balance_after) }}</td>
                                    <td class="small text-muted">
                                        @if ($log->source === 'treatment_record')
                                            療程消費累點
                                        @elseif ($log->source === 'redemption')
                                            兌換療程
                                        @else
                                            {{ $log->note ?? '人工調整' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
