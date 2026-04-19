<?php

namespace App\Services;

use App\Models\JobTitle;
use App\Repositories\JobTitleRepository;
use Illuminate\Database\Eloquent\Collection;

class JobTitleService
{
    public function __construct(
        private readonly JobTitleRepository $jobTitleRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->jobTitleRepository->all();
    }

    public function findById(int $id): ?JobTitle
    {
        return $this->jobTitleRepository->find($id);
    }

    public function create(array $data): JobTitle
    {
        return $this->jobTitleRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->jobTitleRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->jobTitleRepository->delete($id);
    }
}
