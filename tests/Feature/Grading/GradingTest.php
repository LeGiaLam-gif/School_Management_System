<?php

namespace Tests\Feature\Grading;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Contracts\PassedCoursesProvider;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Policies\GradePolicy;
use App\Modules\Grading\Queries\StudentGpaQuery;
use App\Modules\Grading\Support\GradeCalculator;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class GradingTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    private function result(Student $student, Course $course, Semester $semester, float $point, bool $published = true, bool $passed = true): FinalResult
    {
        $section = ClassSection::factory()->for($course)->for($semester)->status(ClassSectionStatus::GradesPublished)->create();
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id, 'class_section_id' => $section->id]);

        return FinalResult::create([
            'enrollment_id' => $enrollment->id,
            'total_score' => 7.0,
            'letter_grade' => 'B',
            'gpa_point' => $point,
            'is_passed' => $passed,
            'published_at' => $published ? now() : null,
        ]);
    }

    public function test_calculator_is_built_from_config(): void
    {
        $calc = app(GradeCalculator::class);

        $this->assertSame('A', $calc->toLetter(9.0));
        $this->assertSame(2.0, $calc->toGpa4(5.5));
        $this->assertFalse($calc->isPassed(3.9));
    }

    public function test_cumulative_gpa_counts_only_published_results_weighted_by_credits(): void
    {
        $student = Student::factory()->create();
        $semester = Semester::factory()->create();
        $a = Course::factory()->credits(3)->create();
        $b = Course::factory()->credits(2)->create();
        $hidden = Course::factory()->credits(4)->create();

        $this->result($student, $a, $semester, 4.0);
        $this->result($student, $b, $semester, 2.0);
        $this->result($student, $hidden, $semester, 0.0, published: false);

        // (4*3 + 2*2) / 5 = 3.2 ; ket qua chua cong bo khong duoc tinh.
        $this->assertSame(3.2, app(StudentGpaQuery::class)->cumulativeGpa($student));
    }

    public function test_semester_gpa_filters_by_semester(): void
    {
        $student = Student::factory()->create();
        $s1 = Semester::factory()->create();
        $s2 = Semester::factory()->create();

        $this->result($student, Course::factory()->credits(3)->create(), $s1, 4.0);
        $this->result($student, Course::factory()->credits(3)->create(), $s2, 2.0);

        $query = app(StudentGpaQuery::class);

        $this->assertSame(4.0, $query->semesterGpa($student, $s1));
        $this->assertSame(2.0, $query->semesterGpa($student, $s2));
        $this->assertSame(3.0, $query->cumulativeGpa($student));
    }

    public function test_gpa_is_null_without_published_results(): void
    {
        $this->assertNull(app(StudentGpaQuery::class)->cumulativeGpa(Student::factory()->create()));
    }

    public function test_passed_courses_provider_returns_only_published_passed_courses(): void
    {
        $student = Student::factory()->create();
        $semester = Semester::factory()->create();
        $passed = Course::factory()->create();
        $failed = Course::factory()->create();
        $unpublished = Course::factory()->create();

        $this->result($student, $passed, $semester, 3.0);
        $this->result($student, $failed, $semester, 0.0, passed: false);
        $this->result($student, $unpublished, $semester, 3.0, published: false);

        $ids = app(PassedCoursesProvider::class)->passedCourseIds($student->id);

        $this->assertSame([$passed->id], $ids);
    }

    public function test_student_sees_only_own_published_result(): void
    {
        $student = Student::factory()->create();
        $other = Student::factory()->create();
        $semester = Semester::factory()->create();

        $own = $this->result($student, Course::factory()->create(), $semester, 3.0);
        $unpublished = $this->result($student, Course::factory()->create(), $semester, 3.0, published: false);
        $foreign = $this->result($other, Course::factory()->create(), $semester, 3.0);

        $user = $student->user;

        $this->assertTrue($user->can('view', $own));
        $this->assertFalse($user->can('view', $unpublished)); // BR-08: chua cong bo
        $this->assertFalse($user->can('view', $foreign));     // IDOR
    }

    public function test_lecturer_manages_grades_only_of_own_unpublished_class(): void
    {
        $lecturer = Lecturer::factory()->create();
        $open = ClassSection::factory()->create(['lecturer_id' => $lecturer->id, 'status' => ClassSectionStatus::Finished]);
        $published = ClassSection::factory()->create(['lecturer_id' => $lecturer->id, 'status' => ClassSectionStatus::GradesPublished]);
        $foreign = ClassSection::factory()->create(['status' => ClassSectionStatus::Finished]);

        $user = $lecturer->user;

        $policy = new GradePolicy;

        $this->assertTrue($policy->manage($user, $open));
        $this->assertFalse($policy->manage($user, $published));  // BR-08: khoa sau cong bo
        $this->assertFalse($policy->manage($user, $foreign));
        $this->assertFalse($policy->unlock($user, $published));  // chi Admin mo khoa
    }
}
