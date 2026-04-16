<?php

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository extends BaseRepository
{
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    public function findByUsername(string $username): ?Admin
    {
        return $this->model->where('username', $username)->first();
    }
}
