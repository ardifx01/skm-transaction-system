<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan role sudah ada
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'customer']);

        // user admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'created_by' => null,
            'updated_by' => null,
        ]);
        $admin->assignRole('admin');

        $ridwan = User::create([
            'name' => 'M Ridwan',
            'email' => 'mridwan07072002@gmail.com',
            'password' => Hash::make('admin123'),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $ridwan->assignRole('admin');

        // user customer
        $customer = User::create([
            'name' => 'customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('admin123'),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $customer->assignRole('customer');
    }
}
