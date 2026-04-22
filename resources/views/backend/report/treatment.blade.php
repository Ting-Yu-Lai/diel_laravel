@extends('backend.layouts.app')

@section('content')
<style>
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

<div class="print-header mb-3">
    <h4 class="mb-0">療程分析報表</h4>
    <small class="text-muted">期間：{{ $from }} 至 {{ $to }} &nbsp;｜&nbsp; 產生時間：{{ now()->format('Y-m-d H:i') }}</small>
    <hr>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
    <h4 class="mb-0"><i class="fa-solid fa-syringe me-2"></i>療程分析報表</h4>
</div>

{{-- 篩選列 --}}
<div class="card mb-4 no-print">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('backend.report.treatment') }}"
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
                        onclick="downloadPdf('療程分析_{{ $from }}_至_{{ $to }}.pdf')">
                    <i class="fa-solid fa-file-pdf me-1"></i>下載 PDF
                </button>
                <a href="{{ route('backend.report.treatment.csv', ['from' => $from, 'to' => $to]) }}"
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
                <div class="text-muted small mb-1">療程總次數</div>
                <div class="fw-bold fs-5 text-primary">{{ number_format($total_times) }}</div>
                <div class="text-muted small mt-1">本期施作</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-success">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">最熱門療程</div>
                <div class="fw-bold text-success" style="font-size: 0.95rem; word-break: break-all;">
                    {{ $top_treatment }}
                </div>
                <div class="text-muted small mt-1">次數最高</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-info">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">期間總營收</div>
                <div class="fw-bold fs-5 text-info">NT$&nbsp;{{ number_format($total_revenue) }}</div>
                <div class="text-muted small mt-1">所有療程合計</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-warning">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">平均毛利率</div>
                <div class="fw-bold fs-5 text-warning">{{ $avg_margin }}%</div>
                <div class="text-muted small mt-1">各療程加權平均</div>
            </div>
        </div>
    </div>
</div>

{{-- PDF 摘要 --}}
<div class="pdf-show mb-3" style="display:none">
    <table class="table table-bordered table-sm text-center">
        <tr class="table-light">
            <th style="width:25%">療程總次數</th>
            <th style="width:25%">最熱門療程</th>
            <th style="width:25%">期間總營收</th>
            <th style="width:25%">平均毛利率</th>
        </tr>
        <tr>
            <td class="fw-bold fs-5">{{ number_format($total_times) }}</td>
            <td class="fw-bold fs-5">{{ $top_treatment }}</td>
            <td class="fw-bold fs-5">NT$&nbsp;{{ number_format($total_revenue) }}</td>
            <td class="fw-bold fs-5">{{ $avg_margin }}%</td>
        </tr>
    </table>
</div>

<div class="row g-4">
    {{-- 熱門療程排行 --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fa-solid fa-ranking-star me-2"></i>熱門療程排行
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:30px">#</th>
                            <th>療程名稱</th>
                            <th class="text-end">次數</th>
                            <th class="text-end">營收</th>
                            <th class="text-end">占比</th>
                            <th class="text-end pe-3">毛利率</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ranking as $i => $row)
                        <tr>
                            <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div>{{ $row->treatment_name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $row->category_name }}</div>
                            </td>
                            <td class="text-end">{{ $row->times }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($row->revenue) }}</td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <div class="progress flex-grow-1 no-print" style="height:6px; min-width:40px; max-width:60px;">
                                        <div class="progress-bar bg-primary" style="width:{{ $row->revenue_pct }}%"></div>
                                    </div>
                                    <span class="badge bg-secondary">{{ $row->revenue_pct }}%</span>
                                </div>
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
                            <td colspan="6" class="text-center text-muted py-3">此期間無療程資料</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($ranking->isNotEmpty())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td class="ps-3" colspan="2">合計</td>
                            <td class="text-end">{{ $ranking->sum('times') }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($ranking->sum('revenue')) }}</td>
                            <td class="text-end">100%</td>
                            <td class="text-end pe-3">{{ $avg_margin }}%</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- 成長趨勢 --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="fa-solid fa-chart-line me-2"></i>月度成長趨勢
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">月份</th>
                            <th class="text-end">次數</th>
                            <th class="text-end">營收</th>
                            <th class="text-end pe-3">環比</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trend as $row)
                        <tr>
                            <td class="ps-3">{{ $row->month }}</td>
                            <td class="text-end">{{ $row->times }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($row->revenue) }}</td>
                            <td class="text-end pe-3">
                                @if($row->growth === null)
                                    <span class="text-muted">—</span>
                                @elseif($row->growth > 0)
                                    <span class="text-success">
                                        <i class="fa-solid fa-arrow-up" style="font-size:0.7rem"></i> {{ $row->growth }}%
                                    </span>
                                @elseif($row->growth < 0)
                                    <span class="text-danger">
                                        <i class="fa-solid fa-arrow-down" style="font-size:0.7rem"></i> {{ abs($row->growth) }}%
                                    </span>
                                @else
                                    <span class="text-muted">0%</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">此期間無資料</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($trend->isNotEmpty())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td class="ps-3">合計</td>
                            <td class="text-end">{{ $trend->sum('times') }}</td>
                            <td class="text-end">NT$&nbsp;{{ number_format($trend->sum('revenue')) }}</td>
                            <td class="text-end pe-3">—</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
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
