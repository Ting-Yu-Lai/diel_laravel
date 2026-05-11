@extends('layouts.member_panel')

@section('title', '會員中心 — DielBeauty')

@section('content')

<div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
    <h1 class="h2 mb-0">會員中心</h1>
    <span class="text-muted small">
        上次登入：{{ $member->last_login_at ? $member->last_login_at->format('Y-m-d H:i') : '首次登入' }}
    </span>
</div>

{{-- 會員資訊摘要卡 --}}
<div class="card shadow-sm mb-4">
    <div class="card-body d-flex align-items-center gap-3 py-3">
        <div style="width:52px; height:52px; border-radius:12px;
                    background:rgba(212,163,115,0.15);
                    display:flex; align-items:center; justify-content:center;
                    font-size:1.4rem; color:#d4a373; flex-shrink:0;">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <div class="fw-semibold fs-5">{{ $member->full_name }}</div>
            <div class="text-muted small">{{ $member->email }}</div>
        </div>
    </div>
</div>

{{-- 三大功能入口（KPI 卡片風格） --}}
<div class="row g-3">
    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('member.profile') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100" style="border-left:4px solid #0d6efd; transition:transform .15s ease, box-shadow .15s ease;"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="width:48px; height:48px; border-radius:10px;
                                background:rgba(13,110,253,0.1);
                                display:flex; align-items:center; justify-content:center;
                                font-size:1.2rem; color:#0d6efd; flex-shrink:0;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="fw-semibold">個人資料</div>
                        <div class="text-muted small">查看與編輯您的聯絡資訊</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted ms-auto" style="font-size:.8rem;"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('member.treatments') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100" style="border-left:4px solid #198754; transition:transform .15s ease, box-shadow .15s ease;"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="width:48px; height:48px; border-radius:10px;
                                background:rgba(25,135,84,0.1);
                                display:flex; align-items:center; justify-content:center;
                                font-size:1.2rem; color:#198754; flex-shrink:0;">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="fw-semibold">療程紀錄</div>
                        <div class="text-muted small">查看歷史療程明細</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted ms-auto" style="font-size:.8rem;"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('member.followUps') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100" style="border-left:4px solid #d4a373; transition:transform .15s ease, box-shadow .15s ease;"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="width:48px; height:48px; border-radius:10px;
                                background:rgba(212,163,115,0.15);
                                display:flex; align-items:center; justify-content:center;
                                font-size:1.2rem; color:#d4a373; flex-shrink:0;">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="fw-semibold">術後追蹤</div>
                        <div class="text-muted small">查看術後照護與追蹤紀錄</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted ms-auto" style="font-size:.8rem;"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('member.points') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100" style="border-left:4px solid #fda085; transition:transform .15s ease, box-shadow .15s ease;"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="width:48px; height:48px; border-radius:10px;
                                background:rgba(253,160,133,0.15);
                                display:flex; align-items:center; justify-content:center;
                                font-size:1.2rem; color:#fda085; flex-shrink:0;">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="fw-semibold">點數中心</div>
                        <div class="text-muted small">
                            累積點數：<span class="fw-bold text-warning">{{ number_format($member->points_balance) }}</span> 點
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted ms-auto" style="font-size:.8rem;"></i>
                </div>
            </div>
        </a>
    </div>
</div>


{{-- 近期登入記錄 --}}
<div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold bg-white border-bottom">
        <i class="fa-solid fa-shield-halved text-secondary me-2"></i>近期登入記錄
        <small class="text-muted fw-normal ms-2">（最近 5 次）</small>
    </div>
    <div class="card-body p-0">
        @if ($loginLogs->isEmpty())
            <div class="text-center text-muted py-4 small">暫無記錄</div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" style="font-size:.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">時間</th>
                            <th>IP 位址</th>
                            <th class="pe-3">裝置 / 瀏覽器</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loginLogs as $log)
                            <tr>
                                <td class="ps-3 text-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td class="text-nowrap">{{ $log->ip_address }}</td>
                                <td class="pe-3 text-muted" style="max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $log->user_agent ? mb_substr($log->user_agent, 0, 80) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
