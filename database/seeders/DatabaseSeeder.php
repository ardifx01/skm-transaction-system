<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed user utama
        // $admin = User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password'), // jangan lupa hash
        //     'created_by' => null,
        //     'updated_by' => null,
        // ]);

        // 2. Seed permission & role
        $this->call([
            PermissionSeeder::class,
            UserRoleSeeder::class,
        ]);

        // 3. Assign role ke admin
        // $admin->assignRole('admin'); // pastikan role ini ada di UserRoleSeeder
    }
}
