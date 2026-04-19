<?php

namespace App\Services;

use App\Models\TreatmentCategory;
use App\Repositories\TreatmentCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class TreatmentCategoryService
{
    public function __construct(
        private readonly TreatmentCategoryRepository $treatmentCategoryRepository,
    ) {}

    public function allWithTreatments(): Collection
    {
        return $this->treatmentCategoryRepository->allWithTreatments();
    }

    public function findById(int $id): ?TreatmentCategory
    {
        return $this->treatmentCategoryRepository->find($id);
    }

    public function create(array $data): TreatmentCategory
    {
        return $this->treatmentCategoryRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->treatmentCategoryRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->treatmentCategoryRepository->delete($id);
    }
}
