<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ======================
        // User Management
        // ======================
        Permission::create(['name' => 'create-users']);
        Permission::create(['name' => 'edit-users']);
        Permission::create(['name' => 'delete-users']);
        Permission::create(['name' => 'view-users']);

        // ======================
        // Role Management
        // ======================
        Permission::create(['name' => 'create-roles']);
        Permission::create(['name' => 'edit-roles']);
        Permission::create(['name' => 'delete-roles']);
        Permission::create(['name' => 'view-roles']);

        // ======================
        // Polis Management
        // ======================
        Permission::create(['name' => 'create-polis']);       // input polis
        Permission::create(['name' => 'edit-polis']);         // edit data polis
        Permission::create(['name' => 'delete-polis']);       // hapus polis
        Permission::create(['name' => 'view-polis']);         // lihat semua polis
        Permission::create(['name' => 'view-own-polis']);     // lihat polis miliknya sendiri
        Permission::create(['name' => 'verify-polis']);       // verifikasi polis
        Permission::create(['name' => 'set-polis-price']);    // set harga premi

        // ======================
        // Payment Management
        // ======================
        Permission::create(['name' => 'confirm-payment']);    // konfirmasi pembayaran
        Permission::create(['name' => 'view-payments']);      // lihat semua pembayaran
        Permission::create(['name' => 'upload-payment-proof']); // upload bukti bayar

        // ======================
        // Laporan
        // ======================
        Permission::create(['name' => 'view-reports']);       // lihat semua laporan
        Permission::create(['name' => 'export-reports']);     // export ke Excel/PDF

        // ======================
        // Profile (Customer)
        // ======================
        Permission::create(['name' => 'view-profile']);
        Permission::create(['name' => 'edit-profile']);

        // ======================
        // Roles & Permissions Assignment
        // ======================

        // Role Admin
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all()); // admin punya semua izin

        // Role Customer
        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'create-polis',
            'edit-polis',
            'delete-polis',
            'view-own-polis',
            'upload-payment-proof',
            'view-profile',
            'edit-profile'
        ]);
    }
}
