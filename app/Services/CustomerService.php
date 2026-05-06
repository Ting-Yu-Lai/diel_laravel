<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Repositories\MemberRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly MemberRepository   $memberRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->customerRepository->all()->loadMissing('tags');
    }

    public function search(string $keyword): Collection
    {
        return $this->customerRepository->search($keyword)->loadMissing('tags');
    }

    public function findById(int $id): ?Customer
    {
        return $this->customerRepository->find($id);
    }

    /**
     * @return array{customer: Customer, member_status: 'created'|'linked'|null, initial_password: string|null}
     */
    public function create(array $data): array
    {
        $customer = $this->customerRepository->create($data);

        [$memberStatus, $initialPassword] = $this->resolveMember($customer, $data);

        return [
            'customer'         => $customer,
            'member_status'    => $memberStatus,
            'initial_password' => $initialPassword,
        ];
    }

    /** @return array{0: 'created'|'linked'|null, 1: string|null} */
    private function resolveMember(Customer $customer, array $data): array
    {
        $email     = $data['email']     ?? null;
        $phone     = $data['phone']     ?? null;
        $idNumber  = $data['id_number'] ?? null;
        $birthDate = $data['birth_date'] ?? null;

        // 先嘗試找已有會員（email 優先，再找電話）
        $existing = $email ? $this->memberRepository->findByEmail($email) : null;
        if (! $existing && $phone) {
            $existing = $this->memberRepository->findByPhone($phone);
        }

        if ($existing) {
            // 已有會員 → 直接關聯，不新建
            $this->customerRepository->update($customer->id, ['member_id' => $existing->id]);
            return ['linked', null];
        }

        // 沒有現有會員 → 需要身分證、生日、email 才能自動建立
        if (! $idNumber || ! $birthDate || ! $email) {
            return [null, null];
        }

        $last4    = substr($idNumber, -4);
        $mmdd     = Carbon::parse($birthDate)->format('md');
        $password = $last4 . $mmdd;

        $member = $this->memberRepository->create([
            'full_name'     => $data['name'],
            'email'         => $email,
            'phone'         => $phone,
            'password_hash' => Hash::make($password),
        ]);

        $this->customerRepository->update($customer->id, ['member_id' => $member->id]);

        return ['created', $password];
    }

    public function update(int $id, array $data): bool
    {
        return $this->customerRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->customerRepository->delete($id);
    }

    public function filterByTag(int $tagId): Collection
    {
        return $this->customerRepository->filterByTag($tagId)->loadMissing('tags');
    }

    public function syncTags(int $id, array $tagIds): void
    {
        $customer = $this->customerRepository->find($id);
        $customer->tags()->sync($tagIds);
    }
}
