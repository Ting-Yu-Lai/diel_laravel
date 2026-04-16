<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function __construct(
        private readonly AdminRepository $adminRepository,
    ) {}

    public function login(string $username, string $password): ?Admin
    {
        $admin = $this->adminRepository->findByUsername($username);

        if (!$admin || !Hash::check($password, $admin->password_hash)) {
            return null;
        }

        $this->adminRepository->update($admin->admin_id, [
            'last_login_at' => now(),
        ]);

        return $admin->fresh();
    }
}
