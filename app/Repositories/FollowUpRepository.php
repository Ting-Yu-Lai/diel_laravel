<?php

namespace App\Repositories;

use App\Models\FollowUp;

class FollowUpRepository extends BaseRepository
{
    public function __construct(FollowUp $model)
    {
        parent::__construct($model);
    }

    public function findWithLogs(int $id): ?FollowUp
    {
        return $this->model->with(['logs.photos'])->find($id);
    }

    public function findByItemId(int $itemId): ?FollowUp
    {
        return $this->model->where('treatment_record_item_id', $itemId)->first();
    }
}
