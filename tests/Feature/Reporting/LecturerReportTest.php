<?php

namespace Tests\Feature\Reporting;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Room;
use App\Modules\Academic\Models\Semester;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Identity\Database\Seeders\RolePermissionSeeder;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\RoomType;
use App\Shared\Enums\SessionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_lecturer_can_view_reports_with_grade_stats_and_attendance_warnings(): void
    {
        $department = Department::query()->create(['code' => 'CNTT', 'name' => 'Công nghệ thông tin']);
        $program = Program::query()->create([
            'department_id' => $department->id,
            'code' => 'KTPM',
            'name' => 'Kỹ thuật phần mềm',
            'total_credits' => 130,
        ]);
        $semester = Semester::query()->create([
            'code' => '2026-HK1',
            'name' => 'Học kỳ 1',
            'academic_year' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-15',
            'is_current' => true,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'code' => 'INT101',
            'name' => 'Lập trình Web',
            'credits' => 3,
        ]);
        $room = Room::query()->create([
            'code' => 'B201',
            'building' => 'Nhà B',
            'capacity' => 50,
            'type' => RoomType::Lecture,
        ]);

        /** @var User $lecturerUser */
        $lecturerUser = User::factory()->create();
        $lecturerUser->assignRole('lecturer');
        $lecturer = Lecturer::query()->create([
            'user_id' => $lecturerUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV001',
        ]);

        $section = ClassSection::query()->create([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $lecturer->id,
            'code' => 'INT101-01',
            'capacity' => 40,
            'enrolled_count' => 1,
            'status' => ClassSectionStatus::InProgress,
        ]);

        /** @var User $studentUser */
        $studentUser = User::factory()->create(['name' => 'Lê Văn Cảnh Báo']);
        $studentUser->assignRole('student');
        $student = Student::query()->create([
            'user_id' => $studentUser->id,
            'program_id' => $program->id,
            'student_code' => 'SV099',
            'cohort_year' => 2024,
        ]);

        $enrollment = Enrollment::query()->create([
            'student_id' => $student->id,
            'class_section_id' => $section->id,
            'status' => EnrollmentStatus::Registered,
        ]);

        // 2 buoi da dien ra, vang 1 buoi = 50% (> nguong 20% BR-09)
        $session1 = ClassSession::query()->create([
            'class_section_id' => $section->id,
            'room_id' => $room->id,
            'session_no' => 1,
            'session_date' => '2026-09-05',
            'start_period' => 1,
            'end_period' => 3,
            'status' => SessionStatus::Held,
        ]);
        $session2 = ClassSession::query()->create([
            'class_section_id' => $section->id,
            'room_id' => $room->id,
            'session_no' => 2,
            'session_date' => '2026-09-12',
            'start_period' => 1,
            'end_period' => 3,
            'status' => SessionStatus::Held,
        ]);

        AttendanceRecord::query()->create([
            'class_session_id' => $session1->id,
            'enrollment_id' => $enrollment->id,
            'status' => AttendanceStatus::Absent,
        ]);
        AttendanceRecord::query()->create([
            'class_session_id' => $session2->id,
            'enrollment_id' => $enrollment->id,
            'status' => AttendanceStatus::Present,
        ]);

        FinalResult::query()->create([
            'enrollment_id' => $enrollment->id,
            'total_score' => 8.50,
            'letter_grade' => 'A',
            'gpa_point' => 4.00,
            'is_passed' => true,
        ]);

        $this->actingAs($lecturerUser)
            ->get(route('lecturer.reports', ['semester_id' => $semester->id]))
            ->assertOk()
            ->assertSee('INT101-01')
            ->assertSee('Lê Văn Cảnh Báo')
            ->assertSee('SV099')
            ->assertSee('50.0%');

        $this->actingAs($lecturerUser)
            ->get(route('lecturer.reports.export', ['semester_id' => $semester->id]))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_student_cannot_access_lecturer_reports(): void
    {
        /** @var User $studentUser */
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');

        $this->actingAs($studentUser)
            ->get(route('lecturer.reports'))
            ->assertForbidden();
    }
}
