<?php

namespace Tests;

use App\Modules\Identity\Database\Seeders\RolePermissionSeeder;

/**
 * Seed role/permission theo ma tran muc 2.5 truoc khi test can phan quyen.
 */
trait CreatesRoles
{
    protected function seedRoles(): void
    {
        $this->seed(RolePermissionSeeder::class);
    }
}
