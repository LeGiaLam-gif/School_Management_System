<?php

namespace App\Modules\Identity\Database\Seeders;

use App\Shared\Authorization\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Tao 3 role (admin, lecturer, student) va permission theo ma tran muc 2.5. Idempotent.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permission::all() as $name) {
            PermissionModel::findOrCreate($name, 'web');
        }

        foreach (Permission::byRole() as $roleName => $permissions) {
            Role::findOrCreate($roleName, 'web')->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
