<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;

class TagService
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {}

    public function findById(int $id): ?Tag
    {
        return $this->tagRepository->find($id);
    }

    public function create(array $data): Tag
    {
        return $this->tagRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tagRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tagRepository->delete($id);
    }
}
