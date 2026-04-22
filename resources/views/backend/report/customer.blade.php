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
    <h4 class="mb-0">客戶分析報表</h4>
    <small class="text-muted">期間：{{ $from }} 至 {{ $to }} &nbsp;｜&nbsp; 產生時間：{{ now()->format('Y-m-d H:i') }}</small>
    <hr>
</div>

{{-- 頁面標題 --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
    <h4 class="mb-0"><i class="fa-solid fa-users me-2"></i>客戶分析報表</h4>
</div>

{{-- 篩選列 --}}
<div class="card mb-4 no-print">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('backend.report.customer') }}"
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
                        onclick="downloadPdf('客戶分析_{{ $from }}_至_{{ $to }}.pdf')">
                    <i class="fa-solid fa-file-pdf me-1"></i>下載 PDF
                </button>
                <a href="{{ route('backend.report.customer.csv', ['from' => $from, 'to' => $to]) }}"
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
        <div class="card h-100 border-success">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">新增客戶數</div>
                <div class="fw-bold fs-5 text-success">
                    {{ $newCustomers['total'] }}
                </div>
                <div class="text-muted small mt-1">本期建檔</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-primary">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">回購率</div>
                <div class="fw-bold fs-5 text-primary">
                    {{ $returnRate['return_rate'] }}%
                </div>
                <div class="text-muted small mt-1">回診客 / 總訪診客</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-warning">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">回訪客戶數</div>
                <div class="fw-bold fs-5 text-warning">
                    {{ $returnRate['returning'] }}
                </div>
                <div class="text-muted small mt-1">本期回診人</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-info">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">總訪診客戶數</div>
                <div class="fw-bold fs-5 text-info">
                    {{ $returnRate['total_visitors'] }}
                </div>
                <div class="text-muted small mt-1">本期到診人次</div>
            </div>
        </div>
    </div>
</div>

{{-- PDF 摘要 --}}
<div class="pdf-show mb-3" style="display:none">
    <table class="table table-bordered table-sm text-center">
        <tr class="table-light">
            <th style="width:25%">新增客戶數</th>
            <th style="width:25%">回購率</th>
            <th style="width:25%">回訪客戶數</th>
            <th style="width:25%">總訪診客戶數</th>
        </tr>
        <tr>
            <td class="fw-bold fs-5">{{ $newCustomers['total'] }}</td>
            <td class="fw-bold fs-5">{{ $returnRate['return_rate'] }}%</td>
            <td class="fw-bold fs-5">{{ $returnRate['returning'] }}</td>
            <td class="fw-bold fs-5">{{ $returnRate['total_visitors'] }}</td>
        </tr>
    </table>
</div>

{{-- 標籤分群 + 新增客戶趨勢 --}}
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fa-solid fa-tags me-2"></i>客戶標籤分群
            </div>
            <div class="card-body p-0">
                @php
                    $totalTagged = $tagSegmentation->sum('customer_count');
                @endphp
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">類別</th>
                            <th>標籤名稱</th>
                            <th class="text-end">客戶數</th>
                            <th class="text-end pe-3">佔比</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagSegmentation as $tag)
                        <tr>
                            <td class="ps-3 text-muted small">{{ $tag->category_name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $tag->tag_name }}</span>
                            </td>
                            <td class="text-end">{{ $tag->customer_count }}</td>
                            <td class="text-end pe-3">
                                @if($totalTagged > 0)
                                    {{ round($tag->customer_count / $totalTagged * 100, 1) }}%
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">尚無標籤資料</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($tagSegmentation->isNotEmpty())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td class="ps-3" colspan="2">合計</td>
                            <td class="text-end">{{ $totalTagged }}</td>
                            <td class="text-end pe-3">100%</td>
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
                <i class="fa-solid fa-chart-bar me-2"></i>新增客戶趨勢
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">月份</th>
                            <th class="text-end pe-3">新增客戶數</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newCustomers['per_month'] as $row)
                        <tr>
                            <td class="ps-3">{{ $row->month }}</td>
                            <td class="text-end pe-3">{{ $row->count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">此期間無新增客戶</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($newCustomers['per_month']) > 0)
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td class="ps-3">合計</td>
                            <td class="text-end pe-3">{{ $newCustomers['total'] }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>

                @if($returnRate['total_visitors'] > 0)
                <div class="px-3 pb-3 pt-2">
                    @php
                        $newPct      = round($returnRate['new_visitors'] / $returnRate['total_visitors'] * 100, 1);
                        $returnPct   = round($returnRate['returning']    / $returnRate['total_visitors'] * 100, 1);
                    @endphp
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>首診 {{ $newPct }}%</span>
                        <span>回診 {{ $returnPct }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $newPct }}%"></div>
                        <div class="progress-bar bg-primary" style="width: {{ $returnPct }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span class="text-success">首診 {{ $returnRate['new_visitors'] }} 人</span>
                        <span class="text-primary">回診 {{ $returnRate['returning'] }} 人</span>
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
