<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Session;

class BackController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function index()
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.loginForm');
        }

        $kpis      = $this->dashboardService->getKpis();
        $chartData = $this->dashboardService->getChartData();
        $rankings  = $this->dashboardService->getRankings();

        return view('backend.dashboard', compact('kpis', 'chartData', 'rankings'));
    }
}
