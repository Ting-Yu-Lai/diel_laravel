<?php

namespace App\Repositories;

use App\Models\Treatment;

class TreatmentRepository extends BaseRepository
{
    public function __construct(Treatment $model)
    {
        parent::__construct($model);
    }

    public function allWithCategory()
    {
        return $this->model->with('treatmentCategory')->get();
    }

    public function filterByCategory(int $categoryId)
    {
        return $this->model->where('treatment_category_id', $categoryId)
            ->with('treatmentCategory')
            ->get();
    }
}
