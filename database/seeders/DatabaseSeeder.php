<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        require_once __DIR__.'/JadwalBimbinganSeeder.php';
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            JadwalBimbinganSeeder::class,
        ]);
    }
}
