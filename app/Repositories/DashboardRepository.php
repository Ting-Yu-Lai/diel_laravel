<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function todayRevenue(): float
    {
        return (float) DB::table('treatment_record_items as tri')
            ->join('treatment_records as tr', 'tri.treatment_record_id', '=', 'tr.id')
            ->whereDate('tr.record_date', Carbon::today())
            ->sum('tri.price');
    }

    public function monthRevenue(string $month): float
    {
        return (float) DB::table('treatment_records')
            ->where('record_month', $month)
            ->sum('total_amount');
    }

    public function monthNewCustomers(string $month): int
    {
        return (int) DB::table('treatment_records')
            ->where('record_month', $month)
            ->where('is_new_customer', 1)
            ->distinct('customer_id')
            ->count('customer_id');
    }

    public function monthReturnStats(string $month): array
    {
        $row = DB::table('treatment_records')
            ->where('record_month', $month)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_return_visit = 1 THEN 1 ELSE 0 END) as returns')
            ->first();

        return [
            'total'   => (int) ($row->total ?? 0),
            'returns' => (int) ($row->returns ?? 0),
        ];
    }

    public function revenueTrend(int $months = 6): Collection
    {
        $from = Carbon::now()->subMonths($months - 1)->format('Y-m');

        return DB::table('treatment_records')
            ->where('record_month', '>=', $from)
            ->selectRaw('record_month, SUM(total_amount) as revenue')
            ->groupBy('record_month')
            ->orderBy('record_month')
            ->get();
    }

    public function treatmentCategoryDistribution(): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('treatments as t', 'tri.treatment_id', '=', 't.id')
            ->join('treatment_categories as tc', 't.treatment_category_id', '=', 'tc.id')
            ->selectRaw('tc.name, SUM(tri.price) as total')
            ->groupBy('tc.id', 'tc.name')
            ->orderByDesc('total')
            ->get();
    }

    public function customerGrowthTrend(int $months = 6): Collection
    {
        $from = Carbon::now()->subMonths($months - 1)->format('Y-m');

        return DB::table('treatment_records')
            ->where('record_month', '>=', $from)
            ->selectRaw('record_month, COUNT(DISTINCT CASE WHEN is_new_customer = 1 THEN customer_id END) as new_customers')
            ->groupBy('record_month')
            ->orderBy('record_month')
            ->get();
    }

    public function topTreatments(int $limit = 5): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('treatments as t', 'tri.treatment_id', '=', 't.id')
            ->selectRaw('t.name, COUNT(tri.id) as item_count, SUM(tri.price) as total_revenue')
            ->groupBy('t.id', 't.name')
            ->orderByDesc('item_count')
            ->limit($limit)
            ->get();
    }

    public function doctorRevenueRanking(int $limit = 5): Collection
    {
        return DB::table('treatment_record_items as tri')
            ->join('staff as s', 'tri.staff_id', '=', 's.id')
            ->whereNotNull('tri.staff_id')
            ->selectRaw('s.name, SUM(tri.price) as total_revenue, COUNT(tri.id) as item_count')
            ->groupBy('s.id', 's.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    public function consultantStats(int $limit = 5): Collection
    {
        return DB::table('treatment_record_staff as trs')
            ->join('staff as s', 'trs.staff_id', '=', 's.id')
            ->join('treatment_records as tr', 'trs.treatment_record_id', '=', 'tr.id')
            ->where('trs.role', 'consultant')
            ->selectRaw('
                s.name,
                COUNT(DISTINCT trs.treatment_record_id) as record_count,
                COUNT(DISTINCT CASE WHEN tr.item_count > 0 THEN trs.treatment_record_id END) as converted_count,
                ROUND(
                    COUNT(DISTINCT CASE WHEN tr.item_count > 0 THEN trs.treatment_record_id END) * 100.0
                    / COUNT(DISTINCT trs.treatment_record_id),
                    1
                ) as conversion_rate,
                SUM(tr.total_amount) as total_revenue
            ')
            ->groupBy('s.id', 's.name')
            ->orderByDesc('conversion_rate')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }
}
