<?php

namespace App\Repositories;

use App\Models\TreatmentCategory;

class TreatmentCategoryRepository extends BaseRepository
{
    public function __construct(TreatmentCategory $model)
    {
        parent::__construct($model);
    }

    public function allWithTreatments()
    {
        return $this->model->with('treatments')->get();
    }
}
