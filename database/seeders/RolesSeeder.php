<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole =  Role::where('name', 'admin')->first();

        if($adminRole == null){
            Role::create(['name' => 'admin']);
        }

        $superAdminRole = Role::where ('name', 'superadmin')->first();

        if ($superAdminRole == null) {
            Role::create(['name' => 'superadmin']);
        }
    }
}
