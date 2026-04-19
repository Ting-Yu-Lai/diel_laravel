<?php

namespace App\Services;

use App\Models\Treatment;
use App\Repositories\TreatmentRepository;
use Illuminate\Database\Eloquent\Collection;

class TreatmentService
{
    public function __construct(
        private readonly TreatmentRepository $treatmentRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->treatmentRepository->allWithCategory();
    }

    public function filterByCategory(int $categoryId): Collection
    {
        return $this->treatmentRepository->filterByCategory($categoryId);
    }

    public function findById(int $id): ?Treatment
    {
        return $this->treatmentRepository->find($id);
    }

    public function create(array $data): Treatment
    {
        return $this->treatmentRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->treatmentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->treatmentRepository->delete($id);
    }

    public function toggle(int $id): Treatment
    {
        $treatment = $this->treatmentRepository->find($id);
        $treatment->is_active = !$treatment->is_active;
        $treatment->save();
        return $treatment;
    }
}
