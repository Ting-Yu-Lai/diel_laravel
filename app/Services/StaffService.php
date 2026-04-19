<?php

namespace App\Services;

use App\Models\Staff;
use App\Repositories\StaffRepository;
use Illuminate\Database\Eloquent\Collection;

class StaffService
{
    public function __construct(
        private readonly StaffRepository $staffRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->staffRepository->all();
    }

    public function searchByName(string $keyword): Collection
    {
        return $this->staffRepository->searchByName($keyword);
    }

    public function filterByJobTitle(int $jobTitleId): Collection
    {
        return $this->staffRepository->filterByJobTitle($jobTitleId);
    }

    public function findById(int $id): ?Staff
    {
        return $this->staffRepository->find($id);
    }

    public function create(array $data): Staff
    {
        return $this->staffRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->staffRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->staffRepository->delete($id);
    }
}
