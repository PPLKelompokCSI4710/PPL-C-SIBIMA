<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define base roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $dosen = Role::firstOrCreate(['name' => 'dosen']);
        $mahasiswa = Role::firstOrCreate(['name' => 'mahasiswa']);

        // Default permissions could go here if needed
        // $viewDashboard = Permission::firstOrCreate(['name' => 'view dashboard']);
        // $admin->givePermissionTo($viewDashboard);
    }
}
