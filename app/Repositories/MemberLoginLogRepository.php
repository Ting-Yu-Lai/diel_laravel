<?php

namespace App\Repositories;

use App\Models\MemberLoginLog;
use Illuminate\Database\Eloquent\Collection;

class MemberLoginLogRepository extends BaseRepository
{
    public function __construct(MemberLoginLog $model)
    {
        parent::__construct($model);
    }

    public function latestForMember(int $memberId, int $limit = 5): Collection
    {
        return $this->model
            ->where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
