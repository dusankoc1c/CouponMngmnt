<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existingUser = User::where('email', 'superadmin@couponmanager.test')->first();

        if ($existingUser == null) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@couponmanager.test',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
            ]);
        }
    }
}
