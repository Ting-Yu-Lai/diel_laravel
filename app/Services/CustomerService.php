<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Database\Eloquent\Collection;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->customerRepository->all();
    }

    public function search(string $keyword): Collection
    {
        return $this->customerRepository->search($keyword);
    }

    public function findById(int $id): ?Customer
    {
        return $this->customerRepository->find($id);
    }

    public function create(array $data): Customer
    {
        return $this->customerRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->customerRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->customerRepository->delete($id);
    }
}
