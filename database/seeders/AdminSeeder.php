<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Admin::create([
            'username' => 'admin',
            'password_hash' => Hash::make('1234'),
            'full_name' => '超級管理員',
            'power' => 1,
            // 'email' => 'admin@example.com'
        ]);
    }
}
