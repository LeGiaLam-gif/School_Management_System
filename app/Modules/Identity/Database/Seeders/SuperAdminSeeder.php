<?php

namespace App\Modules\Identity\Database\Seeders;

use App\Modules\Identity\Models\User;
use App\Shared\Authorization\Permission;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\UserStatus;
use Illuminate\Database\Seeder;

/**
 * Super Admin = role admin + permission roles.manage gan TRUC TIEP (muc 2.2).
 * Thong tin lay tu .env (SUPER_ADMIN_*); khong dat thi bo qua de production khong co tai khoan mac dinh.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            return;
        }

        $user = User::firstOrCreate(['email' => $email], [
            'code' => env('SUPER_ADMIN_CODE', 'SUPERADMIN'),
            'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
            'password' => $password,
            'status' => UserStatus::Active,
        ]);

        $user->assignRole(RoleName::Admin->value);
        $user->givePermissionTo(Permission::ROLES_MANAGE);
    }
}
