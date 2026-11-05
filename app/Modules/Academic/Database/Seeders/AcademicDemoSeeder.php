<?php

namespace App\Modules\Academic\Database\Seeders;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Room;
use App\Modules\Academic\Models\Semester;
use App\Shared\Enums\RoomType;
use Illuminate\Database\Seeder;

/**
 * Du lieu mau cau truc dao tao (chuyen tu seeder cua repo cu: FacultySeeder + AcademicSeeder).
 * Idempotent: chay lai nhieu lan khong sinh ban ghi trung.
 */
class AcademicDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cntt = Department::firstOrCreate(['code' => 'CNTT'], ['name' => 'Công nghệ thông tin']);
        $kt = Department::firstOrCreate(['code' => 'KT'], ['name' => 'Kinh tế']);

        $programs = [
            ['dept' => $cntt, 'code' => 'KTPM', 'name' => 'Kỹ thuật phần mềm'],
            ['dept' => $cntt, 'code' => 'HTTT', 'name' => 'Hệ thống thông tin'],
            ['dept' => $kt, 'code' => 'QTKD', 'name' => 'Quản trị kinh doanh'],
        ];

        foreach ($programs as $p) {
            Program::firstOrCreate(
                ['code' => $p['code']],
                ['department_id' => $p['dept']->id, 'name' => $p['name']]
            );
        }

        $courses = [
            ['IT101', 'Lập trình căn bản', 3],
            ['IT102', 'Cấu trúc dữ liệu và giải thuật', 3],
            ['IT201', 'Cơ sở dữ liệu', 3],
            ['IT202', 'Lập trình web', 3],
            ['IT301', 'Hệ thống thanh toán điện tử', 2],
            ['GE101', 'Lịch sử Đảng Cộng sản Việt Nam', 2],
        ];

        foreach ($courses as [$code, $name, $credits]) {
            Course::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'credits' => $credits,
                    'theory_periods' => $credits * 10,
                    'practice_periods' => $credits * 5,
                    // Mon dai cuong (GE) thuoc Khoa Kinh te trong du lieu mau.
                    'department_id' => str_starts_with($code, 'IT') ? $cntt->id : $kt->id,
                ]
            );
        }

        // IT102 can IT101; IT202 can IT201 (vi du dieu kien tien quyet).
        $this->prerequisite('IT102', 'IT101');
        $this->prerequisite('IT202', 'IT201');

        Semester::firstOrCreate(['code' => '2025-1'], [
            'name' => 'Học kỳ 1',
            'academic_year' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-01-15',
            'registration_open_at' => '2025-08-15 08:00:00',
            'registration_close_at' => '2025-09-14 23:59:59',
            'drop_deadline' => '2025-10-05',
            'is_current' => false,
        ]);

        // Hoc ky hien tai: dot dang ky mo quanh thoi diem seed de demo duoc luong dang ky.
        Semester::firstOrCreate(['code' => '2026-1'], [
            'name' => 'Học kỳ 1',
            'academic_year' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-15',
            'registration_open_at' => now()->subDays(3),
            'registration_close_at' => now()->addDays(14),
            'drop_deadline' => now()->addDays(10)->toDateString(),
            'is_current' => true,
        ]);

        foreach ([['A101', 'A', 60], ['A102', 'A', 60], ['B201', 'B', 80], ['B202', 'B', 80], ['C301', 'C', 120]] as [$code, $building, $capacity]) {
            Room::firstOrCreate(['code' => $code], [
                'building' => $building,
                'capacity' => $capacity,
                'type' => RoomType::Lecture,
            ]);
        }

        Room::firstOrCreate(['code' => 'LAB1'], ['building' => 'C', 'capacity' => 40, 'type' => RoomType::Lab]);
    }

    private function prerequisite(string $courseCode, string $prerequisiteCode): void
    {
        $course = Course::where('code', $courseCode)->firstOrFail();
        $prerequisite = Course::where('code', $prerequisiteCode)->firstOrFail();

        $course->prerequisites()->syncWithoutDetaching([$prerequisite->id]);
    }
}
