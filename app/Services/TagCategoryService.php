<?php

namespace App\Services;

use App\Models\TagCategory;
use App\Repositories\TagCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class TagCategoryService
{
    public function __construct(
        private readonly TagCategoryRepository $tagCategoryRepository,
    ) {}

    public function allWithTags(): Collection
    {
        return $this->tagCategoryRepository->allWithTags();
    }

    public function findById(int $id): ?TagCategory
    {
        return $this->tagCategoryRepository->find($id);
    }

    public function create(array $data): TagCategory
    {
        return $this->tagCategoryRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tagCategoryRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tagCategoryRepository->delete($id);
    }
}
