<?php

namespace App\Repositories;

use App\Models\FollowUpPhoto;

class FollowUpPhotoRepository extends BaseRepository
{
    public function __construct(FollowUpPhoto $model)
    {
        parent::__construct($model);
    }
}
