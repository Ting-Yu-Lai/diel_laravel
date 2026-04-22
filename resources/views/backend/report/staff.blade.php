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
    <h4 class="mb-0">員工績效報表</h4>
    <small class="text-muted">期間：{{ $from }} 至 {{ $to }} &nbsp;｜&nbsp; 產生時間：{{ now()->format('Y-m-d H:i') }}</small>
    <hr>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
    <h4 class="mb-0"><i class="fa-solid fa-user-tie me-2"></i>員工績效報表</h4>
</div>

{{-- 篩選列 --}}
<div class="card mb-4 no-print">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('backend.report.staff') }}" class="row g-2 align-items-end">
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
                        onclick="downloadPdf('員工績效_{{ $from }}_至_{{ $to }}.pdf')">
                    <i class="fa-solid fa-file-pdf me-1"></i>下載 PDF
                </button>
                <a href="{{ route('backend.report.staff.csv', ['from' => $from, 'to' => $to]) }}"
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
        <div class="card h-100 border-primary">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">期間總業績</div>
                <div class="fw-bold fs-5 text-primary">NT$&nbsp;{{ number_format($total_revenue) }}</div>
                <div class="text-muted small mt-1">所有人員合計</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-success">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">最高業績人員</div>
                <div class="fw-bold text-success" style="font-size:0.95rem">{{ $top_staff }}</div>
                <div class="text-muted small mt-1">本期第一名</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-info">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">人員數</div>
                <div class="fw-bold fs-5 text-info">{{ $staff_count }}</div>
                <div class="text-muted small mt-1">有業績人員</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-warning">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1">人均業績</div>
                <div class="fw-bold fs-5 text-warning">NT$&nbsp;{{ number_format($avg_revenue) }}</div>
                <div class="text-muted small mt-1">平均每人</div>
            </div>
        </div>
    </div>
</div>

{{-- PDF 摘要 --}}
<div class="pdf-show mb-3" style="display:none">
    <table class="table table-bordered table-sm text-center">
        <tr class="table-light">
            <th style="width:25%">期間總業績</th>
            <th style="width:25%">最高業績人員</th>
            <th style="width:25%">有業績人員數</th>
            <th style="width:25%">人均業績</th>
        </tr>
        <tr>
            <td class="fw-bold fs-5">NT$&nbsp;{{ number_format($total_revenue) }}</td>
            <td class="fw-bold fs-5">{{ $top_staff }}</td>
            <td class="fw-bold fs-5">{{ $staff_count }}</td>
            <td class="fw-bold fs-5">NT$&nbsp;{{ number_format($avg_revenue) }}</td>
        </tr>
    </table>
</div>

{{-- 員工業績排行 --}}
<div class="card">
    <div class="card-header fw-semibold">
        <i class="fa-solid fa-ranking-star me-2"></i>員工業績排行
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width:36px">#</th>
                    <th>姓名</th>
                    <th>職稱</th>
                    <th class="text-end">施作次數</th>
                    <th class="text-end">業績</th>
                    <th class="text-end">成本</th>
                    <th class="text-end">服務客數</th>
                    <th class="text-end pe-3">客單價</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $i => $row)
                <tr>
                    <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $row->name }}</td>
                    <td><span class="badge bg-secondary">{{ $row->job_title }}</span></td>
                    <td class="text-end">{{ $row->times }}</td>
                    <td class="text-end">NT$&nbsp;{{ number_format($row->revenue) }}</td>
                    <td class="text-end text-muted">NT$&nbsp;{{ number_format($row->cost) }}</td>
                    <td class="text-end">{{ $row->customer_count }}</td>
                    <td class="text-end pe-3">NT$&nbsp;{{ number_format($row->avg_per_customer) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">此期間無業績資料</td>
                </tr>
                @endforelse
            </tbody>
            @if($staff->isNotEmpty())
            <tfoot class="table-light">
                <tr class="fw-semibold">
                    <td class="ps-3" colspan="3">合計</td>
                    <td class="text-end">{{ $staff->sum('times') }}</td>
                    <td class="text-end">NT$&nbsp;{{ number_format($staff->sum('revenue')) }}</td>
                    <td class="text-end">NT$&nbsp;{{ number_format($staff->sum('cost')) }}</td>
                    <td class="text-end">{{ $staff->sum('customer_count') }}</td>
                    <td class="text-end pe-3">—</td>
                </tr>
            </tfoot>
            @endif
        </table>
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
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    }).from(document.querySelector('main')).save().then(() => {
        if (printHeader) printHeader.style.display = '';
        hide.forEach(el => el.style.display = '');
        show.forEach(el => el.style.display = 'none');
    });
}
</script>
@endpush
