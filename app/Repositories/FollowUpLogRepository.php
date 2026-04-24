<?php

namespace App\Repositories;

use App\Models\FollowUpLog;
use App\Models\FollowUpLogDeleteLog;

class FollowUpLogRepository extends BaseRepository
{
    public function __construct(FollowUpLog $model)
    {
        parent::__construct($model);
    }

    public function findWithPhotos(int $id): ?FollowUpLog
    {
        return $this->model->with('photos')->find($id);
    }

    public function createDeleteLog(array $data): void
    {
        FollowUpLogDeleteLog::create($data);
    }

    public function findByFollowUpAndDay(int $followUpId, int $dayNumber): ?FollowUpLog
    {
        return $this->model
            ->where('follow_up_id', $followUpId)
            ->where('day_number', $dayNumber)
            ->first();
    }

    public function hasAnyLog(int $followUpId): bool
    {
        return $this->model->where('follow_up_id', $followUpId)->exists();
    }
}
