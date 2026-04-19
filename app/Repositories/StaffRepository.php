<?php

namespace App\Repositories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Collection;

class StaffRepository extends BaseRepository
{
    public function __construct(Staff $model)
    {
        parent::__construct($model);
    }

    public function searchByName(string $keyword): Collection
    {
        return $this->model
            ->with('jobTitle')
            ->where('name', 'like', "%{$keyword}%")
            ->get();
    }

    public function filterByJobTitle(int $jobTitleId): Collection
    {
        return $this->model
            ->with('jobTitle')
            ->where('job_title_id', $jobTitleId)
            ->get();
    }

    public function all(): Collection
    {
        return $this->model->with('jobTitle')->get();
    }
}
