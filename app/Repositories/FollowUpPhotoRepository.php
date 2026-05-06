<?php

namespace App\Repositories;

use App\Models\FollowUpPhoto;

class FollowUpPhotoRepository extends BaseRepository
{
    public function __construct(FollowUpPhoto $model)
    {
        parent::__construct($model);
    }

    public function hasPreOpPhoto(int $followUpId): bool
    {
        return $this->model
            ->where('follow_up_id', $followUpId)
            ->where('category', 'before')
            ->exists();
    }
}
