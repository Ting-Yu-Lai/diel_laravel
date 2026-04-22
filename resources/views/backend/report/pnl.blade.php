@extends('backend.layouts.app')

@section('content')
<style>
@media print {
    #sidebarMenu, nav.navbar, .no-print { display: none !important; }
    main.col-md-9 { margin-left: 0 !important; max-width: 100% !important; padding: 0 !important; }
    .print-header { display: block !important; }
    .card { border: 1px solid #ccc !important; break-inside: avoid; }
    body { font-size: 12px; }
}
.print-header { display: none; }
</style>

<div class="print-header mb-3">
    <h4 class="mb-0">財務損益報表</h4>
    <small class="text-muted">期間：{{ $from }} 至 {{ $to }} &nbsp;｜&nbsp; 產生時間：{{ now()->format('Y-m-d H:i') }}</small>
    <hr>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
    <h4 class="mb-0"><i class="fa-solid fa-money-bill-trend-up me-2"></i>財務損益報表</h4>
</div>

{{-- 篩選列 --}}
<div class="card mb-4 no-print">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('backend.report.pnl') }}" class="row g-2 align-items-end">
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
                        onclick="downloadPdf('財務損益_{{ $from }}_至_{{ $to }}.pdf')">
                    <i class="fa-solid fa-file-pdf me-1"></i>下載 PDF
                </button>
                <a href="{{ route('backend.report.pnl.csv', ['from' => $from, 'to' => $to]) }}"
                   class="btn btn-outline-success btn-sm">
                    <i class="fa-solid fa-file-csv me-1"></i>匯出 CSV
                </a>
            </div>
        </form>
    </div>
</div>

{{-- KPI 卡片（畫面顯示） --}}
<div class="row g-3 mb-4 pdf-hide">
    <div class="col-6 col-md-3">
        <div class="card h-100 border-success">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">總收入</div>
                <div class="fw-bold fs-5 text-success">NT$&nbsp;{{ number_format($total_revenue) }}</div>
                <div class="text-muted small mt-1">期間營業收入</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-danger">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">總成本</div>
                <div class="fw-bold fs-5 text-danger">NT$&nbsp;{{ number_format($total_cost) }}</div>
                <div class="text-muted small mt-1">期間施作成本</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-primary">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">毛利</div>
                <div class="fw-bold fs-5 {{ $total_profit >= 0 ? 'text-primary' : 'text-danger' }}">
                    NT$&nbsp;{{ number_format($total_profit) }}
                </div>
                <div class="text-muted small mt-1">收入 − 成本</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-warning">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">毛利率</div>
                <div class="fw-bold fs-5 {{ $margin >= 50 ? 'text-success' : ($margin >= 30 ? 'text-warning' : 'text-danger') }}">
                    {{ $margin }}%
                </div>
                <div class="text-muted small mt-1">毛利 / 收入</div>
            </div>
        </div>
    </div>
</div>

{{-- PDF 摘要 --}}
<div class="pdf-show mb-3" style="display:none">
    <table class="table table-bordered table-sm text-center">
        <tr class="table-light">
            <th style="width:25%">總收入</th>
            <th style="width:25%">總成本</th>
            <th style="width:25%">毛利</th>
            <th style="width:25%">毛利率</th>
        </tr>
        <tr>
            <td class="fw-bold fs-5">NT$&nbsp;{{ number_format($total_revenue) }}</td>
            <td class="fw-bold fs-5">NT$&nbsp;{{ number_format($total_cost) }}</td>
            <td class="fw-bold fs-5 {{ $total_profit >= 0 ? 'text-primary' : 'text-danger' }}">
                NT$&nbsp;{{ number_format($total_profit) }}
            </td>
            <td class="fw-bold fs-5">{{ $margin }}%</td>
        </tr>
    </table>
</div>

<div class="row g-4">
    {{-- 月度損益 --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fa-solid fa-calendar-days me-2"></i>月度損益明細
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">月份</th>
                            <th class="text-end">收入</th>
                            <th class="text-end">成本</th>
                            <th class="text-end">毛利</th>
                            <th class="text-end pe-3">毛利率</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthly as $row)
                        <tr>
                            <td class="ps-3">{{ $row->month }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($row->revenue) }}</td>
                            <td class="text-end text-muted">NT$&nbsp;{{ number_format($row->cost) }}</td>
                            <td class="text-end {{ $row->profit >= 0 ? 'text-primary' : 'text-danger' }}">
                                NT$&nbsp;{{ number_format($row->profit) }}
                            </td>
                            <td class="text-end pe-3">
                                @if($row->margin >= 50)
                                    <span class="text-success fw-semibold">{{ $row->margin }}%</span>
                                @elseif($row->margin >= 30)
                                    <span class="text-warning fw-semibold">{{ $row->margin }}%</span>
                                @else
                                    <span class="text-danger fw-semibold">{{ $row->margin }}%</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">此期間無資料</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($monthly->isNotEmpty())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td class="ps-3">合計</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($total_revenue) }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($total_cost) }}</td>
                            <td class="text-end {{ $total_profit >= 0 ? 'text-primary' : 'text-danger' }}">
                                NT$&nbsp;{{ number_format($total_profit) }}
                            </td>
                            <td class="text-end pe-3">{{ $margin }}%</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- 分類損益 --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fa-solid fa-layer-group me-2"></i>各分類損益
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">療程分類</th>
                            <th class="text-end">收入</th>
                            <th class="text-end">毛利</th>
                            <th class="text-end pe-3">毛利率</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($by_category as $row)
                        <tr>
                            <td class="ps-3">{{ $row->category_name }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($row->revenue) }}</td>
                            <td class="text-end {{ $row->profit >= 0 ? 'text-primary' : 'text-danger' }}">
                                NT$&nbsp;{{ number_format($row->profit) }}
                            </td>
                            <td class="text-end pe-3">
                                @if($row->margin >= 50)
                                    <span class="text-success fw-semibold">{{ $row->margin }}%</span>
                                @elseif($row->margin >= 30)
                                    <span class="text-warning fw-semibold">{{ $row->margin }}%</span>
                                @else
                                    <span class="text-danger fw-semibold">{{ $row->margin }}%</span>
                                @endif
                            </td>
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
                            <td class="text-end">NT$&nbsp;{{ number_format($by_category->sum('revenue')) }}</td>
                            <td class="text-end {{ $total_profit >= 0 ? 'text-primary' : 'text-danger' }}">
                                NT$&nbsp;{{ number_format($by_category->sum('profit')) }}
                            </td>
                            <td class="text-end pe-3">{{ $margin }}%</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>

                @if($total_revenue > 0)
                <div class="px-3 pb-3 pt-2">
                    @php $marginPct = min($margin, 100); @endphp
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>成本 {{ 100 - $margin }}%</span>
                        <span>毛利 {{ $margin }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-danger" style="width: {{ 100 - $marginPct }}%"></div>
                        <div class="progress-bar bg-success" style="width: {{ $marginPct }}%"></div>
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
