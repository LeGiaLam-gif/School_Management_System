<?php

namespace Database\Seeders;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Room;
use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSchedule;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\Gender;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\RoomType;
use App\Shared\Enums\SessionStatus;
use App\Shared\Enums\StudentStatus;
use App\Shared\Enums\UserStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Seeder sinh dữ liệu lớn phục vụ kiểm thử chịu tải và chức năng:
 * - 20 Giảng viên (GV001 - GV020)
 * - 40 Lớp (bao gồm 10 Lớp hành chính & 30 Lớp học phần phân bổ theo môn, phòng, giảng viên)
 * - 300 Sinh viên (SV00001 - SV00300) phân bổ đều theo Lớp hành chính, có đăng ký học phần.
 *
 * Idempotent: chạy lại nhiều lần không sinh bản ghi trùng lặp.
 */
class LargeScaleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== BẮT ĐẦU NẠP DỮ LIỆU LỚN (20 GV, 40 LỚP, 300 SV) ===');

        Role::findOrCreate(RoleName::Lecturer->value, 'web');
        Role::findOrCreate(RoleName::Student->value, 'web');

        $commonPassword = Hash::make('password');

        // 1. Kiểm tra Khoa và Ngành
        $cntt = Department::firstOrCreate(['code' => 'CNTT'], ['name' => 'Công nghệ thông tin']);
        $kt = Department::firstOrCreate(['code' => 'KT'], ['name' => 'Kinh tế']);

        $ktpm = Program::firstOrCreate(['code' => 'KTPM'], ['name' => 'Kỹ thuật phần mềm', 'department_id' => $cntt->id]);
        $httt = Program::firstOrCreate(['code' => 'HTTT'], ['name' => 'Hệ thống thông tin', 'department_id' => $cntt->id]);
        $qtkd = Program::firstOrCreate(['code' => 'QTKD'], ['name' => 'Quản trị kinh doanh', 'department_id' => $kt->id]);

        // 2. Sinh đủ 20 Giảng viên
        $this->command->info('-> Đang kiểm tra và tạo 20 Giảng viên...');
        $lecturerHocVi = ['ThS', 'TS', 'PGS.TS'];
        $firstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương'];
        $midNames = ['Văn', 'Thị', 'Đức', 'Quốc', 'Thanh', 'Hữu', 'Ngọc', 'Minh', 'Gia', 'Đình'];
        $lastNames = ['An', 'Bình', 'Cường', 'Dũng', 'Giang', 'Hải', 'Hùng', 'Khoa', 'Long', 'Nam', 'Phong', 'Quân', 'Sơn', 'Tâm', 'Tuấn', 'Việt'];

        for ($i = 1; $i <= 20; $i++) {
            $code = sprintf('GV%03d', $i);
            $email = "gv{$i}@example.com";
            $fn = $firstNames[($i * 3) % count($firstNames)];
            $mn = $midNames[($i * 5) % count($midNames)];
            $ln = $lastNames[($i * 7) % count($lastNames)];
            $name = "{$fn} {$mn} {$ln}";

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'code' => $code,
                    'name' => $name,
                    'password' => $commonPassword,
                    'status' => UserStatus::Active,
                    'must_change_password' => false,
                ]
            );

            if (! $user->hasRole(RoleName::Lecturer->value)) {
                $user->assignRole(RoleName::Lecturer->value);
            }

            Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'lecturer_code' => $code,
                    'department_id' => ($i <= 14) ? $cntt->id : $kt->id,
                    'academic_title' => $lecturerHocVi[$i % count($lecturerHocVi)],
                    'phone' => sprintf('09%08d', 80000000 + $i),
                    'office' => 'P.'.(100 + $i),
                ]
            );
        }
        $allLecturers = Lecturer::orderBy('id')->get();
        $this->command->info('✓ Đã có đủ '.$allLecturers->count().' Giảng viên.');

        // 3. Tạo 10 Lớp hành chính cho các khóa 2021, 2022, 2023, 2024
        $this->command->info('-> Đang kiểm tra và tạo 10 Lớp hành chính...');
        $adminClassData = [
            ['code' => '21DTH01', 'name' => 'Kỹ thuật phần mềm 2021 - Lớp 01', 'program' => $ktpm, 'year' => 2021, 'adv' => 0],
            ['code' => '22DTH01', 'name' => 'Kỹ thuật phần mềm 2022 - Lớp 01', 'program' => $ktpm, 'year' => 2022, 'adv' => 1],
            ['code' => '22DTH02', 'name' => 'Kỹ thuật phần mềm 2022 - Lớp 02', 'program' => $ktpm, 'year' => 2022, 'adv' => 2],
            ['code' => '23DTH01', 'name' => 'Kỹ thuật phần mềm 2023 - Lớp 01', 'program' => $ktpm, 'year' => 2023, 'adv' => 3],
            ['code' => '23DTH02', 'name' => 'Kỹ thuật phần mềm 2023 - Lớp 02', 'program' => $ktpm, 'year' => 2023, 'adv' => 4],
            ['code' => '24DTH01', 'name' => 'Kỹ thuật phần mềm 2024 - Lớp 01', 'program' => $ktpm, 'year' => 2024, 'adv' => 5],
            ['code' => '22DHT01', 'name' => 'Hệ thống thông tin 2022 - Lớp 01', 'program' => $httt, 'year' => 2022, 'adv' => 6],
            ['code' => '23DHT01', 'name' => 'Hệ thống thông tin 2023 - Lớp 01', 'program' => $httt, 'year' => 2023, 'adv' => 7],
            ['code' => '22DQT01', 'name' => 'Quản trị kinh doanh 2022 - Lớp 01', 'program' => $qtkd, 'year' => 2022, 'adv' => 8],
            ['code' => '23DQT01', 'name' => 'Quản trị kinh doanh 2023 - Lớp 01', 'program' => $qtkd, 'year' => 2023, 'adv' => 9],
        ];

        $adminClasses = [];
        foreach ($adminClassData as $item) {
            $adminClasses[] = AdministrativeClass::firstOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'program_id' => $item['program']->id,
                    'academic_year' => $item['year'],
                    'advisor_lecturer_id' => $allLecturers->get($item['adv'])?->id,
                ]
            );
        }
        $this->command->info('✓ Đã có 10 Lớp hành chính.');

        // 4. Sinh thêm các Môn học & Phòng học để đủ tài nguyên mở 30 Lớp học phần
        $this->command->info('-> Đang bổ sung môn học và phòng học...');
        $coursesList = [
            ['IT101', 'Lập trình căn bản', 3, $cntt->id],
            ['IT102', 'Cấu trúc dữ liệu và giải thuật', 3, $cntt->id],
            ['IT201', 'Cơ sở dữ liệu', 3, $cntt->id],
            ['IT202', 'Lập trình web', 3, $cntt->id],
            ['IT301', 'Hệ thống thanh toán điện tử', 2, $cntt->id],
            ['IT302', 'Kiến trúc phần mềm', 3, $cntt->id],
            ['IT303', 'Trí tuệ nhân tạo cơ sở', 3, $cntt->id],
            ['IT304', 'Phát triển ứng dụng di động', 3, $cntt->id],
            ['IT305', 'Mạng máy tính và truyền thông', 3, $cntt->id],
            ['GE101', 'Lịch sử Đảng Cộng sản Việt Nam', 2, $kt->id],
            ['GE102', 'Triết học Mác - Lênin', 3, $kt->id],
            ['BA201', 'Nguyên lý kế toán', 3, $kt->id],
            ['BA202', 'Marketing căn bản', 3, $kt->id],
            ['BA301', 'Quản trị chiến lược', 3, $kt->id],
        ];

        foreach ($coursesList as [$cCode, $cName, $credits, $deptId]) {
            Course::firstOrCreate(
                ['code' => $cCode],
                [
                    'name' => $cName,
                    'credits' => $credits,
                    'theory_periods' => $credits * 10,
                    'practice_periods' => $credits * 5,
                    'department_id' => $deptId,
                    'is_active' => true,
                ]
            );
        }

        for ($r = 1; $r <= 10; $r++) {
            $building = ($r <= 5) ? 'A' : 'B';
            Room::firstOrCreate(
                ['code' => "{$building}{$r}01"],
                [
                    'building' => $building,
                    'capacity' => 60,
                    'type' => RoomType::Lecture,
                    'is_active' => true,
                ]
            );
        }

        $allCourses = Course::all();
        $allRooms = Room::all();
        $currentSemester = Semester::where('is_current', true)->first() ?? Semester::orderByDesc('id')->firstOrFail();
        $oldSemester = Semester::where('code', '2025-1')->first() ?? $currentSemester;

        // 5. Mở 30 Lớp học phần (kết hợp với 10 lớp hành chính = đủ 40 lớp)
        $this->command->info('-> Đang tạo 30 Lớp học phần mở trong kỳ...');
        $sections = [];
        for ($s = 1; $s <= 30; $s++) {
            $targetCourse = $allCourses[$s % $allCourses->count()];
            $targetSemester = ($s <= 10) ? $oldSemester : $currentSemester;
            $secCode = sprintf('%s.%02d', $targetCourse->code, ceil($s / 3));
            $assignedLecturer = $allLecturers[$s % $allLecturers->count()];

            $section = ClassSection::firstOrCreate(
                [
                    'course_id' => $targetCourse->id,
                    'semester_id' => $targetSemester->id,
                    'code' => $secCode,
                ],
                [
                    'lecturer_id' => $assignedLecturer->id,
                    'capacity' => 50,
                    'status' => $targetSemester->is_current ? ClassSectionStatus::Open : ClassSectionStatus::GradesPublished,
                ]
            );

            // Xếp lịch học
            $dayOfWeek = ($s % 6) + 1; // 1 đến 6 (Thứ 2 đến Thứ 7)
            $startPeriod = ($s % 2 === 0) ? 1 : 4;
            $endPeriod = $startPeriod + 2;
            $targetRoom = $allRooms[$s % $allRooms->count()];

            $schedule = ClassSchedule::firstOrCreate(
                [
                    'class_section_id' => $section->id,
                    'day_of_week' => $dayOfWeek,
                    'start_period' => $startPeriod,
                ],
                [
                    'room_id' => $targetRoom->id,
                    'end_period' => $endPeriod,
                    'start_date' => $targetSemester->start_date,
                    'end_date' => $targetSemester->end_date,
                ]
            );

            // Sinh 2 buổi học mẫu
            $startDate = Carbon::parse($targetSemester->start_date);
            foreach ([1, 2] as $sessionNo) {
                ClassSession::firstOrCreate(
                    [
                        'class_section_id' => $section->id,
                        'session_no' => $sessionNo,
                    ],
                    [
                        'class_schedule_id' => $schedule->id,
                        'session_date' => $startDate->copy()->addDays($sessionNo * 7)->toDateString(),
                        'start_period' => $startPeriod,
                        'end_period' => $endPeriod,
                        'room_id' => $targetRoom->id,
                        'status' => SessionStatus::Held,
                    ]
                );
            }

            $sections[] = $section;
        }
        $this->command->info('✓ Đã có 30 Lớp học phần + 10 Lớp hành chính = Tổng cộng 40 Lớp.');

        // 6. Sinh đủ 300 Sinh viên (SV00001 - SV00300)
        $this->command->info('-> Đang kiểm tra và sinh đủ 300 Sinh viên...');
        $genders = [Gender::Male, Gender::Female];

        DB::beginTransaction();
        try {
            for ($st = 1; $st <= 300; $st++) {
                $code = sprintf('SV%05d', $st);
                $email = "sv{$st}@example.com";
                $fn = $firstNames[($st * 2) % count($firstNames)];
                $mn = $midNames[($st * 4) % count($midNames)];
                $ln = $lastNames[($st * 6) % count($lastNames)];
                $name = "{$fn} {$mn} {$ln}";

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'code' => $code,
                        'name' => $name,
                        'password' => $commonPassword,
                        'status' => UserStatus::Active,
                        'must_change_password' => false,
                    ]
                );

                if (! $user->hasRole(RoleName::Student->value)) {
                    $user->assignRole(RoleName::Student->value);
                }

                // Gán vào 1 trong 10 Lớp hành chính và Ngành tương ứng
                $assignedClass = $adminClasses[$st % count($adminClasses)];
                $birthYear = $assignedClass->academic_year - 18;

                $student = Student::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_code' => $code,
                        'program_id' => $assignedClass->program_id,
                        'class_id' => $assignedClass->id,
                        'cohort_year' => $assignedClass->academic_year,
                        'date_of_birth' => sprintf('%04d-%02d-%02d', $birthYear, ($st % 12) + 1, ($st % 28) + 1),
                        'gender' => $genders[$st % 2],
                        'phone' => sprintf('09%08d', 70000000 + $st),
                        'address' => 'Số '.$st.' Đường Võ Văn Ngân, TP. Thủ Đức, TP.HCM',
                        'status' => StudentStatus::Studying,
                    ]
                );

                // Cho mỗi sinh viên đăng ký 2-3 lớp học phần
                $secIndex1 = $st % count($sections);
                $secIndex2 = ($st + 5) % count($sections);

                foreach ([$sections[$secIndex1], $sections[$secIndex2]] as $sec) {
                    /** @var Semester $secSemester */
                    $secSemester = Semester::find($sec->semester_id);
                    Enrollment::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'class_section_id' => $sec->id,
                        ],
                        [
                            'status' => $secSemester->is_current ? EnrollmentStatus::Registered : EnrollmentStatus::Completed,
                            'registered_at' => $secSemester->start_date,
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // Cập nhật lại số lượng sinh viên đăng ký (enrolled_count) trên các lớp học phần
        foreach ($sections as $sec) {
            $count = Enrollment::where('class_section_id', $sec->id)->active()->count();
            $sec->update(['enrolled_count' => $count]);
        }

        $totalLecturers = Lecturer::count();
        $totalAdminClasses = AdministrativeClass::count();
        $totalSections = ClassSection::count();
        $totalStudents = Student::count();

        $this->command->info('====================================================');
        $this->command->info('🎉 HOÀN TẤT NẠP DỮ LIỆU LỚN THÀNH CÔNG:');
        $this->command->info("- Giảng viên: {$totalLecturers} GV (gv1@example.com -> gv{$totalLecturers}@)");
        $this->command->info("- Lớp hành chính: {$totalAdminClasses} lớp");
        $this->command->info("- Lớp học phần: {$totalSections} lớp (Tổng cộng: ".($totalAdminClasses + $totalSections).' lớp)');
        $this->command->info("- Sinh viên: {$totalStudents} SV (sv1@example.com -> sv{$totalStudents}@)");
        $this->command->info('- Mật khẩu đăng nhập chung: "password"');
        $this->command->info('====================================================');
    }
}
