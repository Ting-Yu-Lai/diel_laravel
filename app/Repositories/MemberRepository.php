<?php

namespace App\Repositories;

use App\Models\Member;

class MemberRepository extends BaseRepository
{
    public function __construct(Member $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?Member
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?Member
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function findWithCustomer(int $id): ?Member
    {
        return $this->model->with('customer')->find($id);
    }
}
