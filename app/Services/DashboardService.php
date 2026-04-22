<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $repo,
    ) {}

    public function getKpis(): array
    {
        $month   = Carbon::now()->format('Y-m');
        $stats   = $this->repo->monthReturnStats($month);
        $total   = $stats['total'];
        $returns = $stats['returns'];

        return [
            'today_revenue'   => $this->repo->todayRevenue(),
            'month_revenue'   => $this->repo->monthRevenue($month),
            'new_customers'   => $this->repo->monthNewCustomers($month),
            'repurchase_rate' => $total > 0 ? round($returns / $total * 100, 1) : 0,
            'month_label'     => Carbon::now()->format('Y年m月'),
        ];
    }

    public function getChartData(): array
    {
        $revenueTrend   = $this->repo->revenueTrend(6);
        $categoryDist   = $this->repo->treatmentCategoryDistribution();
        $customerGrowth = $this->repo->customerGrowthTrend(6);

        return [
            'revenue_trend' => [
                'labels' => $revenueTrend->pluck('record_month')->toArray(),
                'data'   => $revenueTrend->pluck('revenue')->map(fn($v) => (float) $v)->toArray(),
            ],
            'category_dist' => [
                'labels' => $categoryDist->pluck('name')->toArray(),
                'data'   => $categoryDist->pluck('total')->map(fn($v) => (float) $v)->toArray(),
            ],
            'customer_growth' => [
                'labels' => $customerGrowth->pluck('record_month')->toArray(),
                'data'   => $customerGrowth->pluck('new_customers')->map(fn($v) => (int) $v)->toArray(),
            ],
        ];
    }

    public function getRankings(): array
    {
        return [
            'top_treatments'   => $this->repo->topTreatments(5),
            'doctor_revenue'   => $this->repo->doctorRevenueRanking(5),
            'consultant_stats' => $this->repo->consultantStats(5),
        ];
    }
}
