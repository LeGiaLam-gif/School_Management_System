<?php

namespace Database\Seeders;

use App\Modules\Academic\Database\Seeders\AcademicDemoSeeder;
use App\Modules\Academic\Database\Seeders\AdministrativeClassSeeder;
use App\Modules\Announcement\Database\Seeders\AnnouncementDemoSeeder;
use App\Modules\Attendance\Database\Seeders\AttendanceDemoSeeder;
use App\Modules\Enrollment\Database\Seeders\EnrollmentDemoSeeder;
use App\Modules\Grading\Database\Seeders\GradingDemoSeeder;
use App\Modules\Identity\Database\Seeders\IdentityDemoSeeder;
use App\Modules\Teaching\Database\Seeders\TeachingDemoSeeder;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Du lieu mau cho demo (muc 10): thu tu = chieu phu thuoc giua cac module.
 * Du lieu nay lay tu seeder cua repo cu (admin/gv/sv, mon hoc, hoc ky, lop, diem, diem danh, thong bao).
 * Quy mo lon (20 GV, 300 SV, 40 lop) se sinh bang Factory o Sprint 5.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Không được nạp dữ liệu demo ở môi trường production.');
        }

        $this->call([
            AcademicDemoSeeder::class,
            IdentityDemoSeeder::class,
            AdministrativeClassSeeder::class,
            TeachingDemoSeeder::class,
            EnrollmentDemoSeeder::class,
            AttendanceDemoSeeder::class,
            GradingDemoSeeder::class,
            AnnouncementDemoSeeder::class,
        ]);
    }
}
