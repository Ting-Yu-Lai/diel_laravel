<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(private ReportRepository $repo) {}

    public function getData(string $from, string $to): array
    {
        $summary    = $this->repo->summary($from, $to);
        $byCategory = $this->repo->revenueByCategory($from, $to);
        $custType   = $this->repo->customerTypeStats($from, $to);

        $total = $summary['total_revenue'];
        $byCategory = $byCategory->map(function ($row) use ($total) {
            $row->percentage = $total > 0 ? round($row->revenue / $total * 100, 1) : 0;
            return $row;
        });

        return [
            'summary'       => $summary,
            'by_category'   => $byCategory,
            'customer_type' => $custType,
        ];
    }

    public function getDetail(string $from, string $to): Collection
    {
        return $this->repo->detail($from, $to);
    }

    public function getStaffData(string $from, string $to): array
    {
        $staff = $this->repo->staffPerformance($from, $to);

        return [
            'staff'         => $staff,
            'total_revenue' => $staff->sum('revenue'),
            'staff_count'   => $staff->count(),
            'top_staff'     => $staff->first()?->name ?? '—',
            'avg_revenue'   => $staff->count() > 0 ? round($staff->avg('revenue')) : 0,
        ];
    }

    public function getStaffDetail(string $from, string $to): Collection
    {
        return $this->repo->staffPerformance($from, $to);
    }

    public function getPnlData(string $from, string $to): array
    {
        $monthly    = $this->repo->pnlMonthly($from, $to);
        $byCategory = $this->repo->pnlByCategory($from, $to);

        $totalRevenue = $monthly->sum('revenue');
        $totalCost    = $monthly->sum('cost');
        $totalProfit  = $monthly->sum('profit');
        $margin       = $totalRevenue > 0 ? round($totalProfit / $totalRevenue * 100, 1) : 0;

        return [
            'monthly'       => $monthly,
            'by_category'   => $byCategory,
            'total_revenue' => $totalRevenue,
            'total_cost'    => $totalCost,
            'total_profit'  => $totalProfit,
            'margin'        => $margin,
        ];
    }

    public function getPnlDetail(string $from, string $to): Collection
    {
        return $this->repo->pnlMonthly($from, $to);
    }

    public function getTreatmentData(string $from, string $to): array
    {
        $ranking = $this->repo->treatmentRanking($from, $to);
        $trend   = $this->repo->treatmentGrowthTrend($from, $to);

        $totalRevenue = $ranking->sum('revenue');
        $ranking = $ranking->map(function ($row) use ($totalRevenue) {
            $row->revenue_pct = $totalRevenue > 0 ? round($row->revenue / $totalRevenue * 100, 1) : 0;
            return $row;
        });

        $prevRevenue = 0;
        $trend = $trend->map(function ($row) use (&$prevRevenue) {
            $row->growth = $prevRevenue > 0 ? round(($row->revenue - $prevRevenue) / $prevRevenue * 100, 1) : null;
            $prevRevenue = $row->revenue;
            return $row;
        });

        return [
            'ranking'       => $ranking,
            'trend'         => $trend,
            'total_revenue' => $totalRevenue,
            'total_times'   => $ranking->sum('times'),
            'avg_margin'    => $ranking->count() > 0 ? round($ranking->avg('margin'), 1) : 0,
            'top_treatment' => $ranking->first()?->treatment_name ?? '—',
        ];
    }

    public function getTreatmentDetail(string $from, string $to): Collection
    {
        return $this->repo->treatmentRanking($from, $to)
            ->sortBy(['category_name', 'treatment_name']);
    }

    public function getCustomerData(string $from, string $to): array
    {
        $newCustomers    = $this->repo->newCustomers($from, $to);
        $returnRate      = $this->repo->returnRate($from, $to);
        $tagSegmentation = $this->repo->tagSegmentation();

        return compact('newCustomers', 'returnRate', 'tagSegmentation');
    }

    public function getCustomerDetail(string $from, string $to): Collection
    {
        return $this->repo->customerDetail($from, $to);
    }
}
