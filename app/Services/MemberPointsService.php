<?php

namespace App\Services;

use App\Models\MemberRedemptionRequest;
use App\Repositories\MemberPointsLogRepository;
use App\Repositories\MemberRedemptionRequestRepository;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\DB;

class MemberPointsService
{
    public function __construct(
        private readonly MemberPointsLogRepository        $logRepo,
        private readonly MemberRepository                 $memberRepo,
        private readonly MemberRedemptionRequestRepository $requestRepo,
    ) {}

    public function earnPoints(int $memberId, int $points, string $source, ?int $sourceId = null, ?string $note = null): void
    {
        DB::transaction(function () use ($memberId, $points, $source, $sourceId, $note) {
            $member = $this->memberRepo->find($memberId);
            $balanceAfter = $member->points_balance + $points;

            $this->memberRepo->update($memberId, ['points_balance' => $balanceAfter]);

            $this->logRepo->create([
                'member_id'    => $memberId,
                'type'         => 'earn',
                'points'       => $points,
                'balance_after'=> $balanceAfter,
                'source'       => $source,
                'source_id'    => $sourceId,
                'note'         => $note,
            ]);
        });
    }

    public function deductPoints(int $memberId, int $points, string $source, ?int $sourceId = null, ?string $note = null): bool
    {
        $member = $this->memberRepo->find($memberId);

        if ($member->points_balance < $points) {
            return false;
        }

        DB::transaction(function () use ($memberId, $points, $source, $sourceId, $note, $member) {
            $balanceAfter = $member->points_balance - $points;

            $this->memberRepo->update($memberId, ['points_balance' => $balanceAfter]);

            $this->logRepo->create([
                'member_id'    => $memberId,
                'type'         => 'redeem',
                'points'       => -$points,
                'balance_after'=> $balanceAfter,
                'source'       => $source,
                'source_id'    => $sourceId,
                'note'         => $note,
            ]);
        });

        return true;
    }

    public function adjustPoints(int $memberId, int $delta, string $note): void
    {
        DB::transaction(function () use ($memberId, $delta, $note) {
            $member = $this->memberRepo->find($memberId);
            $balanceAfter = max(0, $member->points_balance + $delta);

            $this->memberRepo->update($memberId, ['points_balance' => $balanceAfter]);

            $this->logRepo->create([
                'member_id'    => $memberId,
                'type'         => 'adjust',
                'points'       => $delta,
                'balance_after'=> $balanceAfter,
                'source'       => 'manual',
                'source_id'    => null,
                'note'         => $note,
            ]);
        });
    }

    public function getHistory(int $memberId)
    {
        return $this->logRepo->findByMember($memberId);
    }

    public function getPendingRedemptionRequests()
    {
        return $this->requestRepo->findPending();
    }

    public function getMyRedemptionRequests(int $memberId)
    {
        return $this->requestRepo->findByMember($memberId);
    }

    public function submitRedemptionRequest(int $memberId, int $treatmentId, int $pointsCost): MemberRedemptionRequest
    {
        return $this->requestRepo->create([
            'member_id'   => $memberId,
            'treatment_id'=> $treatmentId,
            'points_cost' => $pointsCost,
            'status'      => 'pending',
        ]);
    }

    public function approveRedemption(int $requestId): bool
    {
        $request = $this->requestRepo->find($requestId);

        $success = $this->deductPoints(
            memberId: $request->member_id,
            points: $request->points_cost,
            source: 'redemption',
            sourceId: $request->id,
            note: "兌換療程：{$request->treatment->name}",
        );

        if (! $success) {
            return false;
        }

        $this->requestRepo->update($requestId, ['status' => 'approved']);

        return true;
    }

    public function rejectRedemption(int $requestId, ?string $note): void
    {
        $this->requestRepo->update($requestId, [
            'status'     => 'rejected',
            'admin_note' => $note,
        ]);
    }
}
