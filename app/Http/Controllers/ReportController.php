<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $service) {}

    public function revenue(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $data = $this->service->getData($from, $to);

        return view('backend.report.revenue', array_merge($data, compact('from', 'to')));
    }

    public function csv(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $rows = $this->service->getDetail($from, $to);

        $filename = '營收報表_' . $from . '_至_' . $to . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM，讓 Excel 正確顯示中文
            fputcsv($handle, ['日期', '客戶名稱', '療程', '金額', '醫師', '諮詢師']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->record_date,
                    $row->customer_name,
                    $row->treatments ?? '',
                    $row->total_amount,
                    $row->doctors ?? '',
                    $row->consultants ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function staff(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $data = $this->service->getStaffData($from, $to);

        return view('backend.report.staff', array_merge($data, compact('from', 'to')));
    }

    public function staffCsv(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $rows = $this->service->getStaffDetail($from, $to);

        $filename = '員工績效_' . $from . '_至_' . $to . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['員工ID', '姓名', '職稱', '施作次數', '業績', '成本', '客單價', '服務客戶數']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->name,
                    $row->job_title,
                    $row->times,
                    $row->revenue,
                    $row->cost,
                    $row->avg_per_customer,
                    $row->customer_count,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pnl(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $data = $this->service->getPnlData($from, $to);

        return view('backend.report.pnl', array_merge($data, compact('from', 'to')));
    }

    public function pnlCsv(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $rows = $this->service->getPnlDetail($from, $to);

        $filename = '財務損益_' . $from . '_至_' . $to . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['月份', '總收入', '總成本', '毛利', '毛利率']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->month,
                    $row->revenue,
                    $row->cost,
                    $row->profit,
                    $row->margin . '%',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function treatment(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $data = $this->service->getTreatmentData($from, $to);

        return view('backend.report.treatment', array_merge($data, compact('from', 'to')));
    }

    public function treatmentCsv(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $rows = $this->service->getTreatmentDetail($from, $to);

        $filename = '療程分析_' . $from . '_至_' . $to . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['療程種類', '療程名稱', '次數', '營收', '成本', '毛利', '毛利率']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->category_name,
                    $row->treatment_name,
                    $row->times,
                    $row->revenue,
                    $row->cost,
                    $row->profit,
                    $row->margin . '%',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function customer(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $data = $this->service->getCustomerData($from, $to);

        return view('backend.report.customer', array_merge($data, compact('from', 'to')));
    }

    public function customerCsv(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $rows = $this->service->getCustomerDetail($from, $to);

        $filename = '客戶分析_' . $from . '_至_' . $to . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['客戶ID', '姓名', '最後來診日', '總消費金額', '標籤']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->name,
                    $row->last_visit_date,
                    $row->total_spending,
                    $row->tags ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function parseDateRange(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to',   now()->toDateString());
        return [$from, $to];
    }
}
