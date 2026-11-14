<?php

namespace Tests\Feature\Attendance;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Room;
use App\Modules\Academic\Models\Semester;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Database\Seeders\RolePermissionSeeder;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\SessionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_lecturer_can_view_and_submit_attendance_for_own_session(): void
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

        /** @var Room $room */
        $room = Room::factory()->create();

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
            'status' => ClassSectionStatus::Open,
        ]);

        $session = ClassSession::query()->create([
            'class_section_id' => $section->id,
            'room_id' => $room->id,
            'session_no' => 1,
            'session_date' => '2026-09-07',
            'start_period' => 1,
            'end_period' => 3,
            'status' => SessionStatus::Scheduled,
        ]);

        /** @var User $studentUser */
        $studentUser = User::factory()->create(['name' => 'Nguyễn Văn A']);
        $studentUser->assignRole('student');
        $student = Student::query()->create([
            'user_id' => $studentUser->id,
            'program_id' => $program->id,
            'student_code' => 'SV001',
            'cohort_year' => 2024,
        ]);

        $enrollment = Enrollment::query()->create([
            'student_id' => $student->id,
            'class_section_id' => $section->id,
            'status' => EnrollmentStatus::Registered,
        ]);

        $this->actingAs($lecturerUser)
            ->get(route('lecturer.attendance', [
                'class_section_id' => $section->id,
                'class_session_id' => $session->id,
            ]))
            ->assertOk()
            ->assertSee('INT101-01')
            ->assertSee('Nguyễn Văn A');

        $this->actingAs($lecturerUser)
            ->post(route('lecturer.attendance.store', $session), [
                'session_status' => SessionStatus::Held->value,
                'records' => [
                    $enrollment->id => [
                        'status' => AttendanceStatus::Absent->value,
                        'note' => 'Nghỉ không phép',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_records', [
            'class_session_id' => $session->id,
            'enrollment_id' => $enrollment->id,
            'status' => AttendanceStatus::Absent->value,
            'note' => 'Nghỉ không phép',
            'marked_by' => $lecturerUser->id,
        ]);

        $this->assertDatabaseHas('class_sessions', [
            'id' => $session->id,
            'status' => SessionStatus::Held->value,
        ]);
    }

    public function test_lecturer_cannot_submit_attendance_for_other_lecturers_session(): void
    {
        $department = Department::query()->create(['code' => 'CNTT2', 'name' => 'Khoa CNTT']);
        $program = Program::query()->create([
            'department_id' => $department->id,
            'code' => 'HTTT',
            'name' => 'Hệ thống thông tin',
            'total_credits' => 130,
        ]);
        $semester = Semester::query()->create([
            'code' => '2026-HK2',
            'name' => 'Học kỳ 2',
            'academic_year' => '2026-2027',
            'start_date' => '2027-02-01',
            'end_date' => '2027-06-15',
            'is_current' => false,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'code' => 'INT202',
            'name' => 'Cơ sở dữ liệu',
            'credits' => 3,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create();

        /** @var User $ownerUser */
        $ownerUser = User::factory()->create();
        $ownerUser->assignRole('lecturer');
        $owner = Lecturer::query()->create([
            'user_id' => $ownerUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV010',
        ]);

        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        $otherUser->assignRole('lecturer');
        Lecturer::query()->create([
            'user_id' => $otherUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV011',
        ]);

        $section = ClassSection::query()->create([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $owner->id,
            'code' => 'INT202-01',
            'capacity' => 40,
            'enrolled_count' => 1,
            'status' => ClassSectionStatus::Open,
        ]);

        $session = ClassSession::query()->create([
            'class_section_id' => $section->id,
            'room_id' => $room->id,
            'session_no' => 1,
            'session_date' => '2027-02-08',
            'start_period' => 1,
            'end_period' => 3,
            'status' => SessionStatus::Scheduled,
        ]);

        /** @var User $studentUser */
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = Student::query()->create([
            'user_id' => $studentUser->id,
            'program_id' => $program->id,
            'student_code' => 'SV010',
            'cohort_year' => 2024,
        ]);

        $enrollment = Enrollment::query()->create([
            'student_id' => $student->id,
            'class_section_id' => $section->id,
            'status' => EnrollmentStatus::Registered,
        ]);

        $this->actingAs($otherUser)
            ->post(route('lecturer.attendance.store', $session), [
                'records' => [
                    $enrollment->id => ['status' => AttendanceStatus::Present->value],
                ],
            ])
            ->assertForbidden();

        $this->assertSame(0, AttendanceRecord::query()->count());
    }
}
