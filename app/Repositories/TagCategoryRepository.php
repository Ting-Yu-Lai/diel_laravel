<?php

namespace App\Repositories;

use App\Models\TagCategory;

class TagCategoryRepository extends BaseRepository
{
    public function __construct(TagCategory $model)
    {
        parent::__construct($model);
    }

    public function allWithTags()
    {
        return $this->model->with('tags')->get();
    }
}
