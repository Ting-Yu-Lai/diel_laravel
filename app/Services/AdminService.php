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

    public function login(string $username, string $password): ?Admin
    {
        $admin = $this->adminRepository->findByUsername($username);

        if (!$admin || !Hash::check($password, $admin->password_hash)) {
            return null;
        }

        $this->adminRepository->update($admin->id, [
            'last_login_at' => now(),
        ]);

        Session::put('admin_id', $admin->id);
        Session::put('power', $admin->power);

        return $admin->fresh();
    }
}
