@extends('backend.layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
.kpi-card {
    border: none;
    border-radius: 12px;
    transition: transform .15s ease, box-shadow .15s ease;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important;
}
.kpi-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}
.rank-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    font-weight: 700;
}
</style>

<div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
    <h1 class="h2 mb-0">Dashboard</h1>
    <span class="text-muted small">{{ $kpis['month_label'] }} 數據</span>
</div>

{{-- ── 上方：KPI 卡片 ── --}}
<div class="row g-3 mb-4">

    {{-- 今日營收 --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="text-muted small mb-1">今日營收</div>
                    <div class="fw-bold fs-4 text-truncate">
                        NT$ {{ number_format($kpis['today_revenue']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 本月營收 --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="text-muted small mb-1">本月營收</div>
                    <div class="fw-bold fs-4 text-truncate">
                        NT$ {{ number_format($kpis['month_revenue']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 新客數 --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="text-muted small mb-1">本月新客數</div>
                    <div class="fw-bold fs-4">
                        {{ $kpis['new_customers'] }} <small class="fs-6 fw-normal text-muted">位</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 回購率 --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon bg-info bg-opacity-10 text-info">
                    <i class="fa-solid fa-rotate-right"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="text-muted small mb-1">本月回購率</div>
                    <div class="fw-bold fs-4">
                        {{ $kpis['repurchase_rate'] }}<small class="fs-6 fw-normal text-muted">%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── 中間：圖表 ── --}}
<div class="row g-3 mb-4">

    {{-- 營收趨勢（折線圖） --}}
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white border-bottom d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chart-line text-primary me-2"></i>營收趨勢（近6個月）</span>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart" height="110"></canvas>
            </div>
        </div>
    </div>

    {{-- 療程分布（圓餅圖） --}}
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white border-bottom">
                <i class="fa-solid fa-chart-pie text-warning me-2"></i>療程分類分布
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if (count($chartData['category_dist']['labels']) > 0)
                    <canvas id="categoryPieChart" height="200"></canvas>
                @else
                    <p class="text-muted small mb-0">尚無療程資料</p>
                @endif
            </div>
        </div>
    </div>

    {{-- 客戶成長（長條圖） --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white border-bottom">
                <i class="fa-solid fa-users text-success me-2"></i>新客成長趨勢（近6個月）
            </div>
            <div class="card-body">
                <canvas id="customerGrowthChart" height="60"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- ── 下方：排名 ── --}}
<div class="row g-3">

    {{-- 熱門療程 Top 5 --}}
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white border-bottom">
                <i class="fa-solid fa-fire text-danger me-2"></i>熱門療程 Top 5
            </div>
            <div class="card-body p-0">
                @forelse ($rankings['top_treatments'] as $i => $row)
                    <div class="d-flex align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span class="rank-num me-3
                            {{ $i === 0 ? 'bg-warning text-dark' :
                               ($i === 1 ? 'bg-secondary text-white' :
                               ($i === 2 ? 'bg-danger bg-opacity-75 text-white' : 'bg-light text-muted')) }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate small">{{ $row->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">
                                共 {{ $row->item_count }} 次 ・ NT$ {{ number_format($row->total_revenue) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-center text-muted small">尚無資料</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 醫師營收排名 --}}
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white border-bottom">
                <i class="fa-solid fa-user-doctor text-primary me-2"></i>醫師營收排名
            </div>
            <div class="card-body p-0">
                @forelse ($rankings['doctor_revenue'] as $i => $row)
                    <div class="d-flex align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span class="rank-num me-3
                            {{ $i === 0 ? 'bg-warning text-dark' :
                               ($i === 1 ? 'bg-secondary text-white' :
                               ($i === 2 ? 'bg-danger bg-opacity-75 text-white' : 'bg-light text-muted')) }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small">{{ $row->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">
                                {{ $row->item_count }} 項 ・ NT$ {{ number_format($row->total_revenue) }}
                            </div>
                        </div>
                        <div class="ms-2 text-end">
                            <div class="fw-bold small text-primary">
                                NT$ {{ number_format($row->total_revenue) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-center text-muted small">尚無資料</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 諮詢師成交率 --}}
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white border-bottom">
                <i class="fa-solid fa-handshake text-success me-2"></i>諮詢師成交率
            </div>
            <div class="card-body p-0">
                @forelse ($rankings['consultant_stats'] as $i => $row)
                    <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rank-num
                                    {{ $i === 0 ? 'bg-warning text-dark' :
                                       ($i === 1 ? 'bg-secondary text-white' :
                                       ($i === 2 ? 'bg-danger bg-opacity-75 text-white' : 'bg-light text-muted')) }}">
                                    {{ $i + 1 }}
                                </span>
                                <span class="fw-semibold small">{{ $row->name }}</span>
                            </div>
                            <span class="fw-bold small text-success">{{ $row->conversion_rate }}%</span>
                        </div>
                        <div class="progress" style="height:5px;">
                            <div class="progress-bar bg-success" style="width:{{ min($row->conversion_rate, 100) }}%"></div>
                        </div>
                        <div class="text-muted mt-1" style="font-size:.72rem;">
                            成交 {{ $row->converted_count }}/{{ $row->record_count }} 筆 ・
                            業績 NT$ {{ number_format($row->total_revenue) }}
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-center text-muted small">尚無資料</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ── Chart.js 初始化 ── --}}
<script>
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: { legend: { display: false } },
};

// 營收趨勢折線圖
(function () {
    const labels = @json($chartData['revenue_trend']['labels']);
    const data   = @json($chartData['revenue_trend']['data']);
    if (!labels.length) return;

    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: '營收',
                data,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,.08)',
                borderWidth: 2.5,
                tension: .35,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd',
            }],
        },
        options: {
            ...chartDefaults,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' NT$ ' + ctx.parsed.y.toLocaleString(),
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'NT$ ' + (v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v),
                        maxTicksLimit: 6,
                    },
                    grid: { color: 'rgba(0,0,0,.04)' },
                },
                x: { grid: { display: false } },
            },
        },
    });
})();

// 療程分布圓餅圖
(function () {
    const canvas = document.getElementById('categoryPieChart');
    if (!canvas) return;
    const labels = @json($chartData['category_dist']['labels']);
    const data   = @json($chartData['category_dist']['data']);
    if (!labels.length) return;

    const palette = [
        '#0d6efd','#20c997','#fd7e14','#6f42c1','#dc3545',
        '#0dcaf0','#ffc107','#198754','#d63384','#adb5bd',
    ];

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: palette.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            cutout: '62%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 10, font: { size: 11 } },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ' NT$ ' + ctx.parsed.toLocaleString(),
                    },
                },
            },
        },
    });
})();

// 客戶成長長條圖
(function () {
    const labels = @json($chartData['customer_growth']['labels']);
    const data   = @json($chartData['customer_growth']['data']);
    if (!labels.length) return;

    new Chart(document.getElementById('customerGrowthChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: '新客數',
                data,
                backgroundColor: 'rgba(25,135,84,.75)',
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            ...chartDefaults,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => ' ' + ctx.parsed.y + ' 位' },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, maxTicksLimit: 6 },
                    grid: { color: 'rgba(0,0,0,.04)' },
                },
                x: { grid: { display: false } },
            },
        },
    });
})();
</script>

@endsection
