<?php

namespace App\Modules\Identity\Database\Seeders;

use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Authorization\Permission;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\UserStatus;
use Illuminate\Database\Seeder;

/**
 * Tai khoan mau (chuyen tu UserSeeder cu), mat khau chung: `password`.
 *  - superadmin@example.com (Super Admin), admin@example.com
 *  - gv1@example.com ... gv3@example.com (ma GV001..GV003)
 *  - sv1@example.com ... sv12@example.com (ma SV00001..SV00012)
 * CHI dung cho local/testing/demo; DemoDataSeeder da chan o production.
 */
class IdentityDemoSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = $this->account('superadmin@example.com', 'SA001', 'Super Admin', RoleName::Admin);
        $superAdmin->givePermissionTo(Permission::ROLES_MANAGE);

        $this->account('admin@example.com', 'AD001', 'Quản trị viên', RoleName::Admin);

        $cntt = Department::where('code', 'CNTT')->firstOrFail();
        $degrees = ['ThS', 'TS', 'ThS'];
        $lecturerNames = ['Trần Văn An', 'Lê Thị Bình', 'Phạm Quốc Cường'];

        foreach ($lecturerNames as $i => $name) {
            $n = $i + 1;
            $code = sprintf('GV%03d', $n);
            $user = $this->account("gv{$n}@example.com", $code, $name, RoleName::Lecturer);

            Lecturer::firstOrCreate(['user_id' => $user->id], [
                'lecturer_code' => $code,
                'department_id' => $cntt->id,
                'academic_title' => $degrees[$i],
            ]);
        }

        $programs = Program::orderBy('id')->get();
        $studentNames = [
            'Nguyễn Đình Huy', 'Nguyễn Minh Anh', 'Trần Quang Bảo', 'Lê Hoài Châu',
            'Phạm Gia Hân', 'Hoàng Đức Khang', 'Vũ Thu Lan', 'Đặng Minh Nhật',
            'Bùi Thanh Phương', 'Ngô Anh Quân', 'Đỗ Khánh Linh', 'Dương Tuấn Kiệt',
        ];

        foreach ($studentNames as $i => $name) {
            $n = $i + 1;
            $code = sprintf('SV%05d', $n);
            $user = $this->account("sv{$n}@example.com", $code, $name, RoleName::Student);

            Student::firstOrCreate(['user_id' => $user->id], [
                'student_code' => $code,
                'program_id' => $programs[$i % $programs->count()]->id,
                'cohort_year' => $i % 2 === 0 ? 2022 : 2023,
                'date_of_birth' => now()->subYears(20)->subDays($n * 17)->toDateString(),
            ]);
        }
    }

    private function account(string $email, string $code, string $name, RoleName $role): User
    {
        $user = User::firstOrCreate(['email' => $email], [
            'code' => $code,
            'name' => $name,
            'password' => 'password',
            'status' => UserStatus::Active,
        ]);

        $user->assignRole($role->value);

        return $user;
    }
}
