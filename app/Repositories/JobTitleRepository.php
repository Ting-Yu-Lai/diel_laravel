<?php

namespace App\Repositories;

use App\Models\JobTitle;

class JobTitleRepository extends BaseRepository
{
    public function __construct(JobTitle $model)
    {
        parent::__construct($model);
    }
}
