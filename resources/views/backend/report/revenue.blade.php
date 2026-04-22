@extends('backend.layouts.app')

@section('content')
<style>
/* ── 列印樣式 ─────────────────────────────────────── */
@media print {
    #sidebarMenu,
    nav.navbar,
    .no-print { display: none !important; }

    main.col-md-9 {
        margin-left: 0 !important;
        max-width: 100% !important;
        padding: 0 !important;
    }

    .print-header { display: block !important; }
    .card { border: 1px solid #ccc !important; break-inside: avoid; }
    .badge { border: 1px solid #999; color: #000 !important; background: #eee !important; }

    body { font-size: 12px; }
}

.print-header { display: none; }
</style>

{{-- 頁首標題（列印時顯示） --}}
<div class="print-header mb-3">
    <h4 class="mb-0">營收報表</h4>
    <small class="text-muted">期間：{{ $from }} 至 {{ $to }} &nbsp;｜&nbsp; 產生時間：{{ now()->format('Y-m-d H:i') }}</small>
    <hr>
</div>

{{-- 頁面標題 + 操作按鈕 --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
    <h4 class="mb-0"><i class="fa-solid fa-chart-line me-2"></i>營收報表</h4>
</div>

{{-- 篩選列 --}}
<div class="card mb-4 no-print">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('backend.report.revenue') }}"
              class="row g-2 align-items-end">
            <div class="col-12 col-sm-auto">
                <label class="form-label fw-semibold mb-1">開始日期</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-12 col-sm-auto">
                <label class="form-label fw-semibold mb-1">結束日期</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-12 col-sm-auto d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>查詢
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm"
                        onclick="downloadPdf('營收報表_{{ $from }}_至_{{ $to }}.pdf')">
                    <i class="fa-solid fa-file-pdf me-1"></i>下載 PDF
                </button>
                <a href="{{ route('backend.report.revenue.csv', ['from' => $from, 'to' => $to]) }}"
                   class="btn btn-outline-success btn-sm">
                    <i class="fa-solid fa-file-csv me-1"></i>匯出 CSV
                </a>
            </div>
        </form>
    </div>
</div>

{{-- KPI 卡片 --}}
<div class="row g-3 mb-4 pdf-hide">
    <div class="col-6 col-md-3">
        <div class="card h-100 border-primary">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">總營收</div>
                <div class="fw-bold fs-5 text-primary">
                    NT$&nbsp;{{ number_format($summary['total_revenue']) }}
                </div>
                <div class="text-muted small mt-1">共 {{ $summary['total_records'] }} 筆</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-info">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">客單價</div>
                <div class="fw-bold fs-5 text-info">
                    NT$&nbsp;{{ number_format($summary['avg_transaction']) }}
                </div>
                <div class="text-muted small mt-1">平均每筆</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-success">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">新客數</div>
                <div class="fw-bold fs-5 text-success">
                    {{ $summary['new_count'] }}
                </div>
                <div class="text-muted small mt-1">首次到診</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-warning">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">回診筆數</div>
                <div class="fw-bold fs-5 text-warning">
                    {{ $summary['return_count'] }}
                </div>
                <div class="text-muted small mt-1">本期回診</div>
            </div>
        </div>
    </div>
</div>

{{-- PDF 摘要（下載 PDF 時顯示，取代小卡） --}}
<div class="pdf-show mb-3" style="display:none">
    <table class="table table-bordered table-sm text-center">
        <tr class="table-light">
            <th style="width:25%">總營收</th>
            <th style="width:25%">客單價</th>
            <th style="width:25%">新客數</th>
            <th style="width:25%">回診筆數</th>
        </tr>
        <tr>
            <td class="fw-bold fs-5">NT$&nbsp;{{ number_format($summary['total_revenue']) }}</td>
            <td class="fw-bold fs-5">NT$&nbsp;{{ number_format($summary['avg_transaction']) }}</td>
            <td class="fw-bold fs-5">{{ $summary['new_count'] }}</td>
            <td class="fw-bold fs-5">{{ $summary['return_count'] }}</td>
        </tr>
    </table>
</div>

{{-- 各療程分類營收 + 新舊客分析 --}}
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fa-solid fa-layer-group me-2"></i>各療程分類營收
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">療程分類</th>
                            <th class="text-end">營收</th>
                            <th class="text-end">占比</th>
                            <th class="text-end pe-3">件數</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($by_category as $cat)
                        <tr>
                            <td class="ps-3">{{ $cat->name }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($cat->revenue) }}</td>
                            <td class="text-end">
                                <span class="badge bg-secondary">{{ $cat->percentage }}%</span>
                            </td>
                            <td class="text-end pe-3">{{ $cat->item_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">此期間無資料</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($by_category->isNotEmpty())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td class="ps-3">合計</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($summary['total_revenue']) }}</td>
                            <td class="text-end">100%</td>
                            <td class="text-end pe-3">{{ $by_category->sum('item_count') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fa-solid fa-users me-2"></i>新客 / 舊客分析
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">客型</th>
                            <th class="text-end">筆數</th>
                            <th class="text-end">營收</th>
                            <th class="text-end pe-3">客單價</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-success">新客</span>
                            </td>
                            <td class="text-end">{{ $customer_type['new']['count'] }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($customer_type['new']['revenue']) }}</td>
                            <td class="text-end pe-3">NT$&nbsp;{{ number_format($customer_type['new']['avg']) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-primary">舊客</span>
                            </td>
                            <td class="text-end">{{ $customer_type['existing']['count'] }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($customer_type['existing']['revenue']) }}</td>
                            <td class="text-end pe-3">NT$&nbsp;{{ number_format($customer_type['existing']['avg']) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td class="ps-3">合計</td>
                            <td class="text-end">{{ $summary['total_records'] }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($summary['total_revenue']) }}</td>
                            <td class="text-end pe-3">NT$&nbsp;{{ number_format($summary['avg_transaction']) }}</td>
                        </tr>
                    </tfoot>
                </table>

                @php
                    $total = $summary['total_records'];
                    $newPct = $total > 0 ? round($customer_type['new']['count'] / $total * 100, 1) : 0;
                    $oldPct = $total > 0 ? round($customer_type['existing']['count'] / $total * 100, 1) : 0;
                @endphp
                @if($total > 0)
                <div class="px-3 pb-3 pt-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>新客 {{ $newPct }}%</span>
                        <span>舊客 {{ $oldPct }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $newPct }}%"></div>
                        <div class="progress-bar bg-primary" style="width: {{ $oldPct }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPdf(filename) {
    const hide = document.querySelectorAll('.no-print, .pdf-hide');
    const show = document.querySelectorAll('.pdf-show');
    const printHeader = document.querySelector('.print-header');

    if (printHeader) printHeader.style.display = 'block';
    hide.forEach(el => el.style.display = 'none');
    show.forEach(el => el.style.display = 'block');

    html2pdf().set({
        margin: 10,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(document.querySelector('main')).save().then(() => {
        if (printHeader) printHeader.style.display = '';
        hide.forEach(el => el.style.display = '');
        show.forEach(el => el.style.display = 'none');
    });
}
</script>
@endpush
