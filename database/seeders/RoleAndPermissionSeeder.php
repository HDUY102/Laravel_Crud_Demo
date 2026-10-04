<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo danh sách quyền cho module Product
        $permissions = [
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Tạo Roles và gán Quyền
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $adminRole->syncPermissions($permissions);

        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'sanctum']);
        $staffRole->syncPermissions(['product.view', 'product.create', 'product.edit']);

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'sanctum']);
        $userRole->syncPermissions(['product.view']);
    }
}
