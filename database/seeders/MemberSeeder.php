<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         Member::create([
        'username' => 'seed01',
        'email' => 'seed01@example.com',
        'password_hash' => Hash::make('1234'),
        'full_name' => 'Seeder 測試',
    ]);
    }
}
