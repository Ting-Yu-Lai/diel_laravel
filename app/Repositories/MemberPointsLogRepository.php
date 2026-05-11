<?php

namespace App\Repositories;

use App\Models\MemberPointsLog;
use Illuminate\Database\Eloquent\Collection;

class MemberPointsLogRepository extends BaseRepository
{
    public function __construct(MemberPointsLog $model)
    {
        parent::__construct($model);
    }

    public function findByMember(int $memberId): Collection
    {
        return $this->model
            ->where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->get();
    }
}
