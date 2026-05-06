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
        $digits = preg_replace('/\D/', '', $keyword);

        return $this->model
            ->where(function ($q) use ($keyword, $digits) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
                if ($digits !== '') {
                    $q->orWhere('phone', 'like', "%{$digits}%");
                }
            })
            ->get();
    }

    public function filterByTag(int $tagId): Collection
    {
        return $this->model
            ->whereHas('tags', fn($q) => $q->where('tags.id', $tagId))
            ->get();
    }
}
