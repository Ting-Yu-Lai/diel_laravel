<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminService
{
    public function __construct(
        private readonly AdminRepository $adminRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->adminRepository->all();
    }

    public function findById(int $id): ?Admin
    {
        return $this->adminRepository->find($id);
    }

    public function create(array $data): Admin
    {
        return $this->adminRepository->create([
            'username'      => $data['username'],
            'password_hash' => Hash::make($data['password']),
            'full_name'     => $data['full_name'] ?? null,
            'power'         => $data['power'],
        ]);
    }

    public function update(int $id, array $data): void
    {
        $payload = [
            'username'  => $data['username'],
            'full_name' => $data['full_name'] ?? null,
            'power'     => $data['power'],
        ];

        if (!empty($data['password'])) {
            $payload['password_hash'] = Hash::make($data['password']);
        }

        $this->adminRepository->update($id, $payload);
    }

    public function delete(int $id): void
    {
        $this->adminRepository->delete($id);
    }

    public function login(string $username, string $password): ?Admin
    {
        $admin = $this->adminRepository->findByUsername($username);

        if (!$admin || !Hash::check($password, $admin->password_hash)) {
            return null;
        }

        $this->adminRepository->update($admin->id, ['last_login_at' => now()]);

        Session::put('admin_id', $admin->id);
        Session::put('power',    $admin->power);
        Session::put('full_name', $admin->full_name);

        return $admin->fresh();
    }
}
