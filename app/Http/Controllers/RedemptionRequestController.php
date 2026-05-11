<?php

namespace App\Http\Controllers;

use App\Services\MemberPointsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RedemptionRequestController extends Controller
{
    public function __construct(
        private readonly MemberPointsService $memberPointsService,
    ) {}

    public function index(): View
    {
        $requests = $this->memberPointsService->getPendingRedemptionRequests();

        return view('backend.redemption-request.index', compact('requests'));
    }

    public function approve(int $id): RedirectResponse
    {
        $success = $this->memberPointsService->approveRedemption($id);

        if (! $success) {
            return redirect()->route('backend.redemption-request.index')
                ->with('error', '會員點數不足，無法核准兌換。');
        }

        return redirect()->route('backend.redemption-request.index')
            ->with('success', '兌換申請已核准，點數已扣除。');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $this->memberPointsService->rejectRedemption($id, $request->input('admin_note'));

        return redirect()->route('backend.redemption-request.index')
            ->with('success', '兌換申請已拒絕。');
    }
}
