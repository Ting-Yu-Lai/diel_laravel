<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$keyword}%")
            ->orWhere('phone', 'like', "%{$keyword}%")
            ->orWhere('email', 'like', "%{$keyword}%")
            ->get();
    }

    public function filterByTag(int $tagId): Collection
    {
        return $this->model
            ->whereHas('tags', fn($q) => $q->where('tags.id', $tagId))
            ->get();
    }
}
