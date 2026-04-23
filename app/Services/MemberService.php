<?php

namespace App\Services;

use App\Models\Member;
use App\Repositories\CustomerRepository;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\Hash;

class MemberService
{
    public function __construct(
        private readonly MemberRepository   $memberRepository,
        private readonly CustomerRepository $customerRepository,
    ) {}

    public function register(array $data): Member
    {
        $member = $this->memberRepository->create([
            'password_hash' => Hash::make($data['password']),
            'full_name'     => $data['full_name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
        ]);

        // 若後台已建立同電話的顧客檔案，直接連結；否則新建
        $existing = $this->customerRepository->findBy('phone', $data['phone']);

        if ($existing) {
            $this->customerRepository->update($existing->id, [
                'member_id' => $member->id,
            ]);
        } else {
            $this->customerRepository->create([
                'member_id' => $member->id,
                'name'      => $data['full_name'],
                'email'     => $data['email'],
                'phone'     => $data['phone'],
                'source'    => 'online',
            ]);
        }

        return $member;
    }

    public function login(string $login, string $password): ?Member
    {
        $member = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? $this->memberRepository->findByEmail($login)
            : $this->memberRepository->findByPhone($login);

        if (!$member || !Hash::check($password, $member->password_hash)) {
            return null;
        }

        $this->memberRepository->update($member->id, ['last_login_at' => now()]);

        return $member->fresh();
    }

    public function getProfile(int $memberId): ?Member
    {
        $member = $this->memberRepository->findWithCustomer($memberId);

        if ($member && !$member->customer) {
            $customer = $member->phone
                ? $this->customerRepository->findBy('phone', $member->phone)
                : null;

            if (!$customer && $member->email) {
                $customer = $this->customerRepository->findBy('email', $member->email);
            }

            if ($customer && !$customer->member_id) {
                $this->customerRepository->update($customer->id, ['member_id' => $member->id]);
                $member->load('customer');
            }
        }

        return $member;
    }

    public function updateProfile(int $memberId, array $data): void
    {
        $this->memberRepository->update($memberId, [
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $member = $this->memberRepository->findWithCustomer($memberId);

        if ($member?->customer) {
            $this->customerRepository->update($member->customer->id, [
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);
        }
    }
}
