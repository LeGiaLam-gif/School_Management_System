<?php

namespace Tests\Feature\Seeding;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Services\CourseService;
use App\Modules\Academic\Services\SemesterService;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\Grade;
use App\Modules\Grading\Models\GradeComponent;
use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSchedule;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Kiem tra cac rang buoc toan ven muc 4.3 va 8 o tang DB va tang Service.
 */
class SchemaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_table_in_the_data_dictionary_exists(): void
    {
        $tables = [
            'users', 'students', 'lecturers', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions',
            'role_has_permissions', 'departments', 'programs', 'courses', 'course_prerequisites', 'program_courses',
            'semesters', 'rooms', 'class_sections', 'class_schedules', 'class_sessions', 'enrollments',
            'attendance_records', 'grade_components', 'grades', 'final_results', 'announcements', 'audit_logs',
            'notifications', 'jobs', 'failed_jobs', 'sessions', 'cache',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Thiếu bảng {$table}");
        }
    }

    public function test_enrollment_is_unique_per_student_and_class(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->expectException(QueryException::class);
        Enrollment::factory()->create([
            'student_id' => $enrollment->student_id,
            'class_section_id' => $enrollment->class_section_id,
        ]);
    }

    public function test_attendance_is_unique_per_session_and_enrollment(): void
    {
        $section = ClassSection::factory()->create();
        $schedule = ClassSchedule::factory()->create(['class_section_id' => $section->id]);
        $session = ClassSession::create([
            'class_section_id' => $section->id, 'class_schedule_id' => $schedule->id, 'session_no' => 1,
            'session_date' => '2026-09-07', 'start_period' => 1, 'end_period' => 3, 'room_id' => $schedule->room_id,
        ]);
        $enrollment = Enrollment::factory()->create(['class_section_id' => $section->id]);

        AttendanceRecord::create(['class_session_id' => $session->id, 'enrollment_id' => $enrollment->id]);

        $this->expectException(QueryException::class);
        AttendanceRecord::create(['class_session_id' => $session->id, 'enrollment_id' => $enrollment->id]);
    }

    public function test_grade_is_unique_per_enrollment_and_component(): void
    {
        $enrollment = Enrollment::factory()->create();
        $component = GradeComponent::create([
            'class_section_id' => $enrollment->class_section_id, 'name' => 'Giữa kỳ', 'weight' => 40,
        ]);

        Grade::create(['enrollment_id' => $enrollment->id, 'grade_component_id' => $component->id, 'score' => 7]);

        $this->expectException(QueryException::class);
        Grade::create(['enrollment_id' => $enrollment->id, 'grade_component_id' => $component->id, 'score' => 8]);
    }

    public function test_class_section_code_is_unique_per_course_and_semester(): void
    {
        $section = ClassSection::factory()->create(['code' => 'CS101.01']);

        $this->expectException(QueryException::class);
        ClassSection::factory()->create([
            'course_id' => $section->course_id,
            'semester_id' => $section->semester_id,
            'code' => 'CS101.01',
        ]);
    }

    public function test_same_class_code_is_allowed_in_another_semester(): void
    {
        $section = ClassSection::factory()->create(['code' => '01']);

        $other = ClassSection::factory()->create(['course_id' => $section->course_id, 'code' => '01']);

        $this->assertNotSame($section->semester_id, $other->semester_id);
    }

    public function test_student_user_is_one_to_one(): void
    {
        $student = Student::factory()->create();

        $this->expectException(QueryException::class);
        Student::factory()->create(['user_id' => $student->user_id]);
    }

    public function test_courses_and_class_sections_are_soft_deleted(): void
    {
        $section = ClassSection::factory()->create();

        $section->delete();

        $this->assertSoftDeleted('class_sections', ['id' => $section->id]);
    }

    public function test_course_prerequisite_cycle_is_rejected_by_service(): void
    {
        $a = Course::factory()->create();
        $b = Course::factory()->create();
        $c = Course::factory()->create();
        $service = app(CourseService::class);

        $service->setPrerequisites($b, [$a->id]); // b can a
        $service->setPrerequisites($c, [$b->id]); // c can b

        $this->assertSame([$a->id], $b->prerequisites()->pluck('courses.id')->all());

        $this->expectException(BusinessRuleException::class);
        $service->setPrerequisites($a, [$c->id]);   // a can c => vong
    }

    public function test_setting_prerequisites_replaces_the_previous_set(): void
    {
        $a = Course::factory()->create();
        $b = Course::factory()->create();
        $c = Course::factory()->create();
        $service = app(CourseService::class);

        $service->setPrerequisites($c, [$a->id, $b->id]);
        $service->setPrerequisites($c, [$b->id]);

        $this->assertSame([$b->id], $c->prerequisites()->pluck('courses.id')->all());
    }

    public function test_course_with_class_sections_cannot_be_deleted(): void
    {
        $section = ClassSection::factory()->create();

        $this->expectException(BusinessRuleException::class);
        app(CourseService::class)->delete($section->course);
    }

    public function test_only_one_current_semester(): void
    {
        $first = Semester::factory()->current()->create();
        $second = Semester::factory()->create();

        app(SemesterService::class)->setCurrent($second);

        $this->assertFalse($first->refresh()->is_current);
        $this->assertTrue($second->refresh()->is_current);
        $this->assertSame(1, Semester::current()->count());
    }

    public function test_registration_window_must_open_before_it_closes(): void
    {
        $semester = Semester::factory()->create();

        $this->expectException(BusinessRuleException::class);
        app(SemesterService::class)->scheduleRegistration($semester, now()->addDay(), now());
    }

    public function test_registration_window_and_drop_deadline_helpers(): void
    {
        $semester = Semester::factory()->registrationOpen()->create();

        $this->assertTrue($semester->isRegistrationOpen());
        $this->assertFalse($semester->isRegistrationOpen(now()->addDays(30)));
        $this->assertFalse($semester->isRegistrationOpen(now()->subDays(30)));
        $this->assertTrue($semester->canDropOn(now()));
        $this->assertFalse($semester->canDropOn(now()->addDays(30)));
    }
}
