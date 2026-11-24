<?php

namespace Database\Seeders;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Room;
use App\Modules\Academic\Models\Semester;
use App\Modules\Announcement\Models\Announcement;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Models\Grade;
use App\Modules\Grading\Models\GradeComponent;
use App\Modules\Grading\Support\GradeCalculator;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSchedule;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AnnouncementScope;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\CourseType;
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
 * Seeder sinh dữ liệu phục vụ kiểm thử và demo:
 * - 20 Giảng viên (GV001 - GV020)
 * - 40 Lớp (bao gồm 10 Lớp hành chính & 30 Lớp học phần phân bổ theo môn, phòng, giảng viên)
 * - 20 Sinh viên (SV00001 - SV00020) phân bổ đều theo Lớp hành chính, có đăng ký học phần, điểm danh và GPA riêng biệt.
 *
 * Idempotent: chạy lại nhiều lần không sinh bản ghi trùng lặp.
 */
class LargeScaleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== BẮT ĐẦU NẠP DỮ LIỆU DEMO (20 GV, 40 LỚP, 20 SV) ===');

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

            $lecturer = Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'lecturer_code' => $code,
                    'department_id' => ($i <= 14) ? $cntt->id : $kt->id,
                    'academic_title' => $lecturerHocVi[$i % count($lecturerHocVi)],
                    'phone' => sprintf('09%08d', 80000000 + $i),
                    'office' => 'P.'.(100 + $i),
                    'address' => 'Số '.($i * 15).' Đường Kha Vạn Cân, TP. Thủ Đức, TP.HCM',
                ]
            );

            // Bổ sung đầy đủ SĐT, phòng làm việc, địa chỉ cho các GV đầu nếu còn thiếu
            if ($lecturer->phone === null || $lecturer->office === null || $lecturer->address === null) {
                $lecturer->update([
                    'phone' => $lecturer->phone ?? sprintf('09%08d', 80000000 + $i),
                    'office' => $lecturer->office ?? ('P.'.(100 + $i)),
                    'address' => $lecturer->address ?? ('Số '.($i * 15).' Đường Kha Vạn Cân, TP. Thủ Đức, TP.HCM'),
                ]);
            }
        }
        $allLecturers = Lecturer::orderBy('id')->get();
        $cnttLecturers = $allLecturers->where('department_id', $cntt->id)->values();
        $ktLecturers = $allLecturers->where('department_id', $kt->id)->values();
        $this->command->info('✓ Đã có đủ '.$allLecturers->count().' Giảng viên.');

        // 3. Tạo 10 Lớp hành chính cho các khóa 2021, 2022, 2023, 2024
        $this->command->info('-> Đang kiểm tra và tạo 10 Lớp hành chính...');
        $adminClassData = [
            ['code' => '21DTH01', 'name' => 'Kỹ thuật phần mềm 2021 - Lớp 01', 'program' => $ktpm, 'year' => 2021, 'adv' => $cnttLecturers->get(0)],
            ['code' => '22DTH01', 'name' => 'Kỹ thuật phần mềm 2022 - Lớp 01', 'program' => $ktpm, 'year' => 2022, 'adv' => $cnttLecturers->get(1)],
            ['code' => '22DTH02', 'name' => 'Kỹ thuật phần mềm 2022 - Lớp 02', 'program' => $ktpm, 'year' => 2022, 'adv' => $cnttLecturers->get(2)],
            ['code' => '23DTH01', 'name' => 'Kỹ thuật phần mềm 2023 - Lớp 01', 'program' => $ktpm, 'year' => 2023, 'adv' => $cnttLecturers->get(3)],
            ['code' => '23DTH02', 'name' => 'Kỹ thuật phần mềm 2023 - Lớp 02', 'program' => $ktpm, 'year' => 2023, 'adv' => $cnttLecturers->get(4)],
            ['code' => '24DTH01', 'name' => 'Kỹ thuật phần mềm 2024 - Lớp 01', 'program' => $ktpm, 'year' => 2024, 'adv' => $cnttLecturers->get(5)],
            ['code' => '22DHT01', 'name' => 'Hệ thống thông tin 2022 - Lớp 01', 'program' => $httt, 'year' => 2022, 'adv' => $cnttLecturers->get(6)],
            ['code' => '23DHT01', 'name' => 'Hệ thống thông tin 2023 - Lớp 01', 'program' => $httt, 'year' => 2023, 'adv' => $cnttLecturers->get(7)],
            ['code' => '22DQT01', 'name' => 'Quản trị kinh doanh 2022 - Lớp 01', 'program' => $qtkd, 'year' => 2022, 'adv' => $ktLecturers->get(0)],
            ['code' => '23DQT01', 'name' => 'Quản trị kinh doanh 2023 - Lớp 01', 'program' => $qtkd, 'year' => 2023, 'adv' => $ktLecturers->get(1)],
        ];

        $adminClasses = [];
        foreach ($adminClassData as $item) {
            $cls = AdministrativeClass::firstOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'program_id' => $item['program']->id,
                    'academic_year' => $item['year'],
                    'advisor_lecturer_id' => $item['adv']?->id,
                ]
            );
            if ($cls->advisor_lecturer_id === null && $item['adv'] !== null) {
                $cls->update(['advisor_lecturer_id' => $item['adv']->id]);
            }
            $adminClasses[] = $cls;
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

        // Gắn môn học vào Khung chương trình (program_courses) cho 3 ngành
        $allCoursesByCode = Course::all()->keyBy('code');
        $programCoursePlans = [
            $ktpm->id => [
                ['IT101', 1, CourseType::Required->value],
                ['GE101', 1, CourseType::Required->value],
                ['GE102', 1, CourseType::Required->value],
                ['IT102', 2, CourseType::Required->value],
                ['IT201', 2, CourseType::Required->value],
                ['IT202', 3, CourseType::Required->value],
                ['IT305', 3, CourseType::Required->value],
                ['IT301', 4, CourseType::Elective->value],
                ['IT302', 4, CourseType::Required->value],
                ['IT303', 5, CourseType::Required->value],
                ['IT304', 5, CourseType::Elective->value],
            ],
            $httt->id => [
                ['IT101', 1, CourseType::Required->value],
                ['GE101', 1, CourseType::Required->value],
                ['IT102', 2, CourseType::Required->value],
                ['IT201', 2, CourseType::Required->value],
                ['BA201', 2, CourseType::Elective->value],
                ['IT202', 3, CourseType::Required->value],
                ['IT301', 3, CourseType::Required->value],
                ['IT305', 4, CourseType::Required->value],
                ['IT302', 4, CourseType::Elective->value],
                ['IT303', 5, CourseType::Required->value],
            ],
            $qtkd->id => [
                ['GE101', 1, CourseType::Required->value],
                ['GE102', 1, CourseType::Required->value],
                ['BA201', 2, CourseType::Required->value],
                ['BA202', 2, CourseType::Required->value],
                ['IT101', 3, CourseType::Elective->value],
                ['IT301', 3, CourseType::Required->value],
                ['BA301', 4, CourseType::Required->value],
            ],
        ];

        foreach ($programCoursePlans as $progId => $planItems) {
            /** @var Program|null $progModel */
            $progModel = Program::find($progId);
            if (! $progModel) {
                continue;
            }
            $syncData = [];
            foreach ($planItems as [$cCode, $semNo, $cType]) {
                $cModel = $allCoursesByCode->get($cCode);
                if ($cModel) {
                    $syncData[$cModel->id] = [
                        'suggested_semester_no' => $semNo,
                        'type' => $cType,
                    ];
                }
            }
            $progModel->courses()->syncWithoutDetaching($syncData);
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
        $oldSections = [];
        $currentSections = [];
        $cnttIdx = 0;
        $ktIdx = 0;
        for ($s = 1; $s <= 30; $s++) {
            $targetCourse = $allCourses[$s % $allCourses->count()];
            $targetSemester = ($s <= 10) ? $oldSemester : $currentSemester;
            $secCode = sprintf('%s.%02d', $targetCourse->code, ceil($s / 3));
            if ($targetCourse->department_id === $cntt->id) {
                $assignedLecturer = $cnttLecturers[$cnttIdx % $cnttLecturers->count()];
                $cnttIdx++;
            } else {
                $assignedLecturer = $ktLecturers[$ktIdx % $ktLecturers->count()];
                $ktIdx++;
            }

            $section = ClassSection::updateOrCreate(
                [
                    'course_id' => $targetCourse->id,
                    'semester_id' => $targetSemester->id,
                    'code' => $secCode,
                ],
                [
                    'lecturer_id' => $assignedLecturer->id,
                    'capacity' => 60,
                    'status' => $targetSemester->is_current ? ClassSectionStatus::Open : ClassSectionStatus::GradesPublished,
                ]
            );

            // Cấu hình đầu điểm thành phần cho lớp học phần (Giữa kỳ 40%, Cuối kỳ 60%)
            GradeComponent::firstOrCreate(
                ['class_section_id' => $section->id, 'name' => 'Giữa kỳ'],
                ['weight' => 40, 'max_score' => 10, 'sort_order' => 1]
            );
            GradeComponent::firstOrCreate(
                ['class_section_id' => $section->id, 'name' => 'Cuối kỳ'],
                ['weight' => 60, 'max_score' => 10, 'sort_order' => 2]
            );

            // Xếp lịch học (phủ đều Thứ 2 -> Thứ 7, ca Sáng & Chiều)
            $dayOfWeek = ($s % 6) + 1; // 1 đến 6 (Thứ 2 đến Thứ 7)
            $startPeriods = [1, 4, 7, 10];
            $startPeriod = $startPeriods[$s % count($startPeriods)];
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

            // Sinh 3 buổi học mẫu để đồng bộ thống kê chuyên cần
            $dates = $targetSemester->is_current
                ? [now()->subDays(14), now()->subDays(7), now()]
                : [
                    Carbon::parse($targetSemester->start_date),
                    Carbon::parse($targetSemester->start_date)->addDays(7),
                    Carbon::parse($targetSemester->start_date)->addDays(14),
                ];

            foreach ($dates as $idx => $sessionDate) {
                ClassSession::firstOrCreate(
                    [
                        'class_section_id' => $section->id,
                        'session_no' => $idx + 1,
                    ],
                    [
                        'class_schedule_id' => $schedule->id,
                        'session_date' => $sessionDate->toDateString(),
                        'start_period' => $startPeriod,
                        'end_period' => $endPeriod,
                        'room_id' => $targetRoom->id,
                        'status' => SessionStatus::Held,
                    ]
                );
            }

            // Tạo thông báo lớp học phần gắn với Giảng viên phụ trách
            if ($assignedLecturer->user_id) {
                Announcement::firstOrCreate(
                    [
                        'class_section_id' => $section->id,
                        'title' => "[{$section->code}] Thông báo kế hoạch học tập môn {$targetCourse->name}",
                    ],
                    [
                        'author_id' => $assignedLecturer->user_id,
                        'scope' => AnnouncementScope::ClassSection,
                        'body' => "Giảng viên {$assignedLecturer->user->name} thông báo đề cương, tài liệu và lịch kiểm tra lớp {$section->code} ({$targetCourse->name}).",
                        'is_pinned' => ($s % 5 === 0),
                        'published_at' => now()->subDays($s % 10),
                    ]
                );
            }

            $sections[] = $section;
            if ($targetSemester->is_current) {
                $currentSections[] = $section;
            } else {
                $oldSections[] = $section;
            }
        }
        $this->command->info('✓ Đã có 30 Lớp học phần + 10 Lớp hành chính = Tổng cộng 40 Lớp.');

        // 6. Sinh đủ 20 Sinh viên (SV00001 - SV00020) kèm đăng ký học phần đa dạng
        $this->command->info('-> Đang kiểm tra và sinh đủ 20 Sinh viên...');
        $genders = [Gender::Male, Gender::Female];

        // Dọn dẹp các tài khoản SV00021..SV00300 cũ (nếu DB từng chạy bản 300 SV trước đây) và làm sạch lịch sử đăng ký để tái tạo đồng bộ 100%
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            AttendanceRecord::query()->delete();
            Grade::query()->delete();
            FinalResult::query()->delete();
            Enrollment::query()->delete();
            $extraStudents = Student::where('student_code', '>', 'SV00020')->get();
            if ($extraStudents->isNotEmpty()) {
                $extraStudentIds = $extraStudents->pluck('id')->all();
                $extraUserIds = $extraStudents->pluck('user_id')->all();
                DB::table('academic_warnings')->whereIn('student_id', $extraStudentIds)->delete();
                Student::whereIn('id', $extraStudentIds)->delete();
                User::withTrashed()->whereIn('id', $extraUserIds)->forceDelete();
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        DB::beginTransaction();
        try {
            for ($st = 1; $st <= 20; $st++) {
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

                // Cập nhật thông tin SĐT, địa chỉ, giới tính và Lớp hành chính cho 12 SV đầu nếu còn thiếu
                $resolvedClassId = $student->class_id;
                if ($resolvedClassId === null) {
                    $matchingClass = collect($adminClasses)->first(
                        fn (AdministrativeClass $c): bool => $c->program_id === $student->program_id && $c->academic_year === $student->cohort_year
                    ) ?? collect($adminClasses)->first(
                        fn (AdministrativeClass $c): bool => $c->program_id === $student->program_id
                    ) ?? $assignedClass;
                    $resolvedClassId = $matchingClass->id;
                }

                if ($student->phone === null || $student->address === null || $student->gender === null || $student->class_id === null) {
                    $student->update([
                        'class_id' => $resolvedClassId,
                        'gender' => $student->gender ?? $genders[$st % 2],
                        'phone' => $student->phone ?? sprintf('09%08d', 70000000 + $st),
                        'address' => $student->address ?? ('Số '.$st.' Đường Võ Văn Ngân, TP. Thủ Đức, TP.HCM'),
                    ]);
                }

                // Phân bổ học kỳ cũ (2025-1): mỗi SV tích lũy từ 3 đến 6 môn hoàn toàn khác nhau
                $numOldCourses = 3 + ($st % 4); // 3, 4, 5 hoặc 6 môn kỳ cũ
                $enrolledOldCourseIds = [];

                for ($k = 0; $k < count($oldSections) && count($enrolledOldCourseIds) < $numOldCourses; $k++) {
                    $oldSec = $oldSections[($st * 3 + $k * 2) % count($oldSections)];
                    if (in_array($oldSec->course_id, $enrolledOldCourseIds, true)) {
                        continue;
                    }

                    Enrollment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'class_section_id' => $oldSec->id,
                        ],
                        [
                            'status' => EnrollmentStatus::Completed,
                            'registered_at' => $oldSemester->start_date,
                        ]
                    );
                    $enrolledOldCourseIds[] = $oldSec->course_id;
                }

                // Phân bổ học kỳ hiện tại (2026-1): mỗi SV đăng ký từ 2 đến 4 lớp học phần khác hẳn nhau (Thời khóa biểu khác nhau 100%)
                $numCurrentCourses = 2 + ($st % 3); // 2, 3 hoặc 4 môn kỳ hiện tại
                $enrolledCurCourseIds = [];

                for ($m = 0; $m < count($currentSections) && count($enrolledCurCourseIds) < $numCurrentCourses; $m++) {
                    $curSec = $currentSections[($st * 5 + $m * 3) % count($currentSections)];
                    if (in_array($curSec->course_id, $enrolledCurCourseIds, true)) {
                        continue;
                    }

                    Enrollment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'class_section_id' => $curSec->id,
                        ],
                        [
                            'status' => EnrollmentStatus::Registered,
                            'registered_at' => $currentSemester->start_date,
                        ]
                    );
                    $enrolledCurCourseIds[] = $curSec->course_id;
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // 7. Sinh dữ liệu Điểm danh (AttendanceRecord) và Điểm số (Grade & FinalResult) cho tất cả các lớp
        $this->command->info('-> Đang đồng bộ Điểm danh và Điểm tổng kết (GPA) cho toàn bộ Sinh viên...');
        $calculator = app(GradeCalculator::class);
        $allSystemSections = ClassSection::with(['semester', 'lecturer', 'sessions', 'gradeComponents'])->get();

        DB::beginTransaction();
        try {
            foreach ($allSystemSections as $secIdx => $sec) {
                $secEnrollments = Enrollment::with('student')->where('class_section_id', $sec->id)->orderBy('id')->get();
                $sec->update(['enrolled_count' => $secEnrollments->whereIn('status', [EnrollmentStatus::Registered, EnrollmentStatus::Completed])->count()]);

                // 7.1. Sinh điểm danh đa dạng theo từng sinh viên cho mọi buổi học đã diễn ra
                foreach ($sec->sessions as $session) {
                    if (! $session instanceof ClassSession) {
                        continue;
                    }
                    foreach ($secEnrollments as $eIdx => $enr) {
                        $stNum = $this->resolveStudentNumber($enr);
                        $seedVal = ($stNum * 7 + $session->session_no * 13 + $sec->id * 5) % 20;
                        // Phân hóa tỷ lệ chuyên cần theo từng sinh viên
                        $studentMod = $stNum % 7;
                        if ($studentMod === 0) {
                            // Sinh viên chăm chỉ 100%
                            $attStatus = AttendanceStatus::Present;
                        } elseif ($studentMod <= 2) {
                            // Sinh viên thỉnh thoảng đi muộn hoặc phép
                            $attStatus = $seedVal === 0 ? AttendanceStatus::Late : ($seedVal === 1 ? AttendanceStatus::Excused : AttendanceStatus::Present);
                        } elseif ($studentMod <= 4) {
                            // Sinh viên vắng 1 buổi
                            $attStatus = ($session->session_no === 2 && ($stNum + $sec->id) % 2 === 0)
                                ? AttendanceStatus::Absent
                                : ($seedVal < 2 ? AttendanceStatus::Late : AttendanceStatus::Present);
                        } else {
                            // Sinh viên vắng nhiều hơn để có mức chuyên cần 66.7% - 88.9%
                            $attStatus = ($session->session_no !== 1 && ($stNum + $sec->id + $session->session_no) % 3 === 0)
                                ? AttendanceStatus::Absent
                                : AttendanceStatus::Present;
                        }

                        AttendanceRecord::updateOrCreate(
                            [
                                'class_session_id' => $session->id,
                                'enrollment_id' => $enr->id,
                            ],
                            [
                                'status' => $attStatus,
                                'marked_by' => $sec->lecturer?->user_id,
                                'marked_at' => $session->session_date,
                            ]
                        );
                    }
                }

                // 7.2. Sinh điểm thành phần & tổng kết cho các lớp đã công bố điểm (Học kỳ cũ)
                if ($sec->status !== ClassSectionStatus::GradesPublished) {
                    continue;
                }

                $midComp = $sec->gradeComponents->firstWhere('name', 'Giữa kỳ');
                $finComp = $sec->gradeComponents->firstWhere('name', 'Cuối kỳ');
                if (! $midComp instanceof GradeComponent || ! $finComp instanceof GradeComponent) {
                    continue;
                }

                // Bảng phân hóa năng lực học tập theo từng sinh viên (SV1 -> SV20) để mỗi SV có GPA khác hẳn nhau
                $studentBands = [
                    1 => 5.0,  // SV1: Cảnh báo học vụ / Trung bình yếu
                    2 => 5.4,  // SV2: Cảnh báo học vụ / Trung bình
                    3 => 5.8,  // SV3: Trung bình
                    4 => 5.2,  // SV4: Cảnh báo lần 2
                    5 => 8.6,  // SV5 (Phạm Gia Hân): Giỏi
                    6 => 7.4,  // SV6 (Hoàng Đức Khang): Khá
                    7 => 9.2,  // SV7 (Vũ Thu Lan): Xuất sắc
                    8 => 6.6,  // SV8 (Đặng Minh Nhật): Trung bình khá
                    9 => 8.1,  // SV9 (Bùi Thanh Phương): Khá giỏi
                    10 => 7.8, // SV10 (Ngô Anh Quân): Khá
                    11 => 9.4, // SV11 (Đỗ Khánh Linh): Xuất sắc
                    12 => 6.2, // SV12 (Dương Tuấn Kiệt): Trung bình
                    13 => 6.8, // SV13: Khá
                    14 => 7.6, // SV14: Khá
                    15 => 8.8, // SV15: Giỏi
                    16 => 5.9, // SV16: Trung bình
                    17 => 8.3, // SV17: Giỏi
                    18 => 7.1, // SV18: Khá
                    19 => 9.1, // SV19: Xuất sắc
                    20 => 6.4, // SV20: Trung bình khá
                ];

                foreach ($secEnrollments as $enr) {
                    $stNum = $this->resolveStudentNumber($enr);
                    $baseScore = $studentBands[$stNum] ?? (5.2 + (($stNum * 17) % 43) / 10.0);
                    $courseVariance = ((($stNum * 3 + $sec->course_id * 7) % 15) - 7) / 10.0;

                    $midScore = max(3.5, min(10.0, round($baseScore + $courseVariance, 1)));
                    $finScore = max(3.5, min(10.0, round($baseScore - ($courseVariance * 0.6), 1)));

                    Grade::updateOrCreate(
                        ['enrollment_id' => $enr->id, 'grade_component_id' => $midComp->id],
                        [
                            'score' => $midScore,
                            'graded_by' => $sec->lecturer?->user_id,
                            'graded_at' => $sec->semester->end_date,
                        ]
                    );

                    Grade::updateOrCreate(
                        ['enrollment_id' => $enr->id, 'grade_component_id' => $finComp->id],
                        [
                            'score' => $finScore,
                            'graded_by' => $sec->lecturer?->user_id,
                            'graded_at' => $sec->semester->end_date,
                        ]
                    );

                    $total = $calculator->weightedTotal([
                        ['score' => $midScore, 'max' => 10, 'weight' => 40],
                        ['score' => $finScore, 'max' => 10, 'weight' => 60],
                    ]);

                    if ($total !== null) {
                        FinalResult::updateOrCreate(
                            ['enrollment_id' => $enr->id],
                            [
                                'total_score' => $total,
                                'letter_grade' => $calculator->toLetter($total),
                                'gpa_point' => $calculator->toGpa4($total),
                                'is_passed' => $calculator->isPassed($total),
                                'published_at' => $sec->semester->end_date,
                                'locked_at' => $sec->semester->end_date,
                            ]
                        );
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
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

    /**
     * Lấy số thứ tự sinh viên từ student_code (SV00001 -> 1) để đồng bộ GPA/chuyên cần bất kể AUTO_INCREMENT trên TiDB.
     */
    private function resolveStudentNumber(Enrollment $enrollment): int
    {
        $student = $enrollment->student;
        $code = $student instanceof Student ? $student->student_code : null;
        if (is_string($code) && preg_match('/^SV(\d+)$/', $code, $matches) === 1) {
            return (int) $matches[1];
        }

        return $enrollment->student_id;
    }
}
