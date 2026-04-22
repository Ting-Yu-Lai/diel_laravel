<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function summary(string $from, string $to): array
    {
        $row = DB::table('treatment_records')
            ->whereBetween('record_date', [$from, $to])
            ->selectRaw('
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COUNT(*) as total_records,
                SUM(CASE WHEN is_new_customer = 1 THEN 1 ELSE 0 END) as new_count,
                SUM(CASE WHEN is_return_visit = 1 THEN 1 ELSE 0 END) as return_count
            ')
            ->first();

        $total   = (float) $row->total_revenue;
        $records = (int)   $row->total_records;

        return [
            'total_revenue'   => $total,
            'total_records'   => $records,
            'avg_transaction' => $records > 0 ? round($total / $records) : 0,
            'new_count'       => (int) $row->new_count,
            'return_count'    => (int) $row->return_count,
        ];
    }

    public function revenueByCategory(string $from, string $to): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('treatment_records as tr', 'tri.treatment_record_id', '=', 'tr.id')
            ->join('treatments as t', 'tri.treatment_id', '=', 't.id')
            ->join('treatment_categories as tc', 't.treatment_category_id', '=', 'tc.id')
            ->whereBetween('tr.record_date', [$from, $to])
            ->selectRaw('tc.name, SUM(tri.price) as revenue, COUNT(tri.id) as item_count')
            ->groupBy('tc.id', 'tc.name')
            ->orderByDesc('revenue')
            ->get();
    }

    public function customerTypeStats(string $from, string $to): array
    {
        $newRow = DB::table('treatment_records')
            ->whereBetween('record_date', [$from, $to])
            ->where('is_new_customer', 1)
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_amount), 0) as rev')
            ->first();

        $oldRow = DB::table('treatment_records')
            ->whereBetween('record_date', [$from, $to])
            ->where('is_new_customer', 0)
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_amount), 0) as rev')
            ->first();

        $newCnt = (int)   $newRow->cnt;
        $newRev = (float) $newRow->rev;
        $oldCnt = (int)   $oldRow->cnt;
        $oldRev = (float) $oldRow->rev;

        return [
            'new' => [
                'count'   => $newCnt,
                'revenue' => $newRev,
                'avg'     => $newCnt > 0 ? round($newRev / $newCnt) : 0,
            ],
            'existing' => [
                'count'   => $oldCnt,
                'revenue' => $oldRev,
                'avg'     => $oldCnt > 0 ? round($oldRev / $oldCnt) : 0,
            ],
        ];
    }

    public function staffPerformance(string $from, string $to): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('staff as s', 'tri.staff_id', '=', 's.id')
            ->leftJoin('job_titles as jt', 's.job_title_id', '=', 'jt.id')
            ->join('treatment_records as tr', 'tri.treatment_record_id', '=', 'tr.id')
            ->whereBetween('tr.record_date', [$from, $to])
            ->whereNotNull('tri.staff_id')
            ->selectRaw("
                s.id,
                s.name,
                COALESCE(jt.name, '—') as job_title,
                COUNT(tri.id) as times,
                COALESCE(SUM(tri.price), 0) as revenue,
                COALESCE(SUM(tri.cost), 0) as cost,
                COUNT(DISTINCT tr.customer_id) as customer_count,
                CASE WHEN COUNT(DISTINCT tr.customer_id) > 0
                    THEN ROUND(COALESCE(SUM(tri.price), 0) / COUNT(DISTINCT tr.customer_id))
                    ELSE 0 END as avg_per_customer
            ")
            ->groupBy('s.id', 's.name', 'jt.name')
            ->orderByDesc('revenue')
            ->get();
    }

    public function pnlMonthly(string $from, string $to): Collection
    {
        return DB::table('treatment_records')
            ->whereBetween('record_date', [$from, $to])
            ->selectRaw("
                DATE_FORMAT(record_date, '%Y-%m') as month,
                COALESCE(SUM(total_amount), 0) as revenue,
                COALESCE(SUM(total_cost), 0) as cost,
                COALESCE(SUM(total_profit), 0) as profit,
                CASE WHEN COALESCE(SUM(total_amount), 0) > 0
                    THEN ROUND(COALESCE(SUM(total_profit), 0) / COALESCE(SUM(total_amount), 0) * 100, 1)
                    ELSE 0 END as margin
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function pnlByCategory(string $from, string $to): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('treatments as t', 'tri.treatment_id', '=', 't.id')
            ->join('treatment_categories as tc', 't.treatment_category_id', '=', 'tc.id')
            ->join('treatment_records as tr', 'tri.treatment_record_id', '=', 'tr.id')
            ->whereBetween('tr.record_date', [$from, $to])
            ->selectRaw("
                tc.name as category_name,
                COALESCE(SUM(tri.price), 0) as revenue,
                COALESCE(SUM(tri.cost), 0) as cost,
                COALESCE(SUM(tri.price), 0) - COALESCE(SUM(tri.cost), 0) as profit,
                CASE WHEN COALESCE(SUM(tri.price), 0) > 0
                    THEN ROUND((COALESCE(SUM(tri.price), 0) - COALESCE(SUM(tri.cost), 0)) / COALESCE(SUM(tri.price), 0) * 100, 1)
                    ELSE 0 END as margin
            ")
            ->groupBy('tc.id', 'tc.name')
            ->orderByDesc('revenue')
            ->get();
    }

    public function treatmentRanking(string $from, string $to): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('treatment_records as tr', 'tri.treatment_record_id', '=', 'tr.id')
            ->join('treatments as t', 'tri.treatment_id', '=', 't.id')
            ->join('treatment_categories as tc', 't.treatment_category_id', '=', 'tc.id')
            ->whereBetween('tr.record_date', [$from, $to])
            ->selectRaw("
                tc.name as category_name,
                t.name as treatment_name,
                COUNT(tri.id) as times,
                COALESCE(SUM(tri.price), 0) as revenue,
                COALESCE(SUM(tri.cost), 0) as cost,
                COALESCE(SUM(tri.price), 0) - COALESCE(SUM(tri.cost), 0) as profit,
                CASE WHEN COALESCE(SUM(tri.price), 0) > 0
                    THEN ROUND((COALESCE(SUM(tri.price), 0) - COALESCE(SUM(tri.cost), 0)) / COALESCE(SUM(tri.price), 0) * 100, 1)
                    ELSE 0 END as margin
            ")
            ->groupBy('t.id', 't.name', 'tc.id', 'tc.name')
            ->orderByDesc('revenue')
            ->get();
    }

    public function treatmentGrowthTrend(string $from, string $to): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('treatment_records as tr', 'tri.treatment_record_id', '=', 'tr.id')
            ->whereBetween('tr.record_date', [$from, $to])
            ->selectRaw("
                DATE_FORMAT(tr.record_date, '%Y-%m') as month,
                COUNT(tri.id) as times,
                COALESCE(SUM(tri.price), 0) as revenue
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function newCustomers(string $from, string $to): array
    {
        $perMonth = DB::table('customers')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$from, $to])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $total = DB::table('customers')
            ->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$from, $to])
            ->count();

        return ['total' => $total, 'per_month' => $perMonth];
    }

    public function returnRate(string $from, string $to): array
    {
        $totalVisitors = DB::table('treatment_records')
            ->whereBetween('record_date', [$from, $to])
            ->distinct('customer_id')
            ->count('customer_id');

        $returning = DB::table('treatment_records')
            ->whereBetween('record_date', [$from, $to])
            ->where('is_return_visit', 1)
            ->distinct('customer_id')
            ->count('customer_id');

        $rate = $totalVisitors > 0 ? round($returning / $totalVisitors * 100, 1) : 0;

        return [
            'total_visitors' => $totalVisitors,
            'returning'      => $returning,
            'new_visitors'   => $totalVisitors - $returning,
            'return_rate'    => $rate,
        ];
    }

    public function tagSegmentation(): Collection
    {
        return DB::table('tags as t')
            ->join('tag_categories as tc', 't.tag_category_id', '=', 'tc.id')
            ->leftJoin('customer_tag as ct', 't.id', '=', 'ct.tag_id')
            ->selectRaw('tc.name as category_name, t.name as tag_name, t.id as tag_id, COUNT(ct.customer_id) as customer_count')
            ->groupBy('t.id', 't.name', 'tc.id', 'tc.name')
            ->orderBy('tc.name')
            ->orderByDesc('customer_count')
            ->get();
    }

    public function customerDetail(string $from, string $to): Collection
    {
        return DB::table('customers as c')
            ->join('treatment_records as tr', 'c.id', '=', 'tr.customer_id')
            ->whereBetween('tr.record_date', [$from, $to])
            ->selectRaw("
                c.id,
                c.name,
                MAX(tr.record_date) as last_visit_date,
                SUM(tr.total_amount) as total_spending,
                (
                    SELECT GROUP_CONCAT(DISTINCT tg.name ORDER BY tg.name SEPARATOR ', ')
                    FROM customer_tag ct
                    JOIN tags tg ON ct.tag_id = tg.id
                    WHERE ct.customer_id = c.id
                ) as tags
            ")
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('last_visit_date')
            ->get();
    }

    public function detail(string $from, string $to): Collection
    {
        return DB::table('treatment_records as tr')
            ->join('customers as c', 'tr.customer_id', '=', 'c.id')
            ->whereBetween('tr.record_date', [$from, $to])
            ->selectRaw("
                tr.record_date,
                c.name as customer_name,
                tr.total_amount,
                (
                    SELECT GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ')
                    FROM treatment_record_items tri2
                    JOIN treatments t ON tri2.treatment_id = t.id
                    WHERE tri2.treatment_record_id = tr.id
                ) as treatments,
                (
                    SELECT GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ')
                    FROM treatment_record_staff trs
                    JOIN staff s ON trs.staff_id = s.id
                    WHERE trs.treatment_record_id = tr.id AND trs.role = 'doctor'
                ) as doctors,
                (
                    SELECT GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ')
                    FROM treatment_record_staff trs
                    JOIN staff s ON trs.staff_id = s.id
                    WHERE trs.treatment_record_id = tr.id AND trs.role = 'consultant'
                ) as consultants
            ")
            ->orderBy('tr.record_date')
            ->get();
    }
}
