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
}
