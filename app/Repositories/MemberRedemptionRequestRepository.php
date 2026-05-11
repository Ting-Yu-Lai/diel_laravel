<?php

namespace App\Repositories;

use App\Models\MemberRedemptionRequest;
use Illuminate\Database\Eloquent\Collection;

class MemberRedemptionRequestRepository extends BaseRepository
{
    public function __construct(MemberRedemptionRequest $model)
    {
        parent::__construct($model);
    }

    public function findPending(): Collection
    {
        return $this->model
            ->where('status', 'pending')
            ->with(['member', 'treatment'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function findByMember(int $memberId): Collection
    {
        return $this->model
            ->where('member_id', $memberId)
            ->with('treatment')
            ->orderByDesc('created_at')
            ->get();
    }
}
