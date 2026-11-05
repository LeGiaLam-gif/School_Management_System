<?php

namespace Database\Seeders;

use App\Modules\Identity\Database\Seeders\RolePermissionSeeder;
use App\Modules\Identity\Database\Seeders\SuperAdminSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * `docker compose up` chay `migrate --seed` (NFR-DEP-01): luon tao role/permission (+ Super Admin
     * neu co SUPER_ADMIN_* trong .env); du lieu demo chi nap o local/testing/demo.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);

        if (app()->environment(['local', 'testing', 'demo'])) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
