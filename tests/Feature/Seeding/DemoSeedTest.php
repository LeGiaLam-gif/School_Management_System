<?php

namespace Tests\Feature\Seeding;

use App\Modules\Academic\Models\Course;
use App\Modules\Announcement\Models\Announcement;
use App\Modules\Announcement\Services\AnnouncementService;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Services\AttendanceService;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Queries\StudentGpaQuery;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Authorization\Permission;
use App\Shared\Enums\ClassSectionStatus;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test cho toan bo schema + du lieu mau chuyen tu repo cu: neu migration, model hoac
 * seeder lech nhau, test nay do truoc.
 */
class DemoSeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_demo_data_counts_match_legacy_sample(): void
    {
        $this->assertSame(3, Lecturer::count());
        $this->assertSame(12, Student::count());
        $this->assertSame(6, Course::count());
        $this->assertSame(5, ClassSection::count());
        $this->assertSame(2, ClassSection::where('status', ClassSectionStatus::GradesPublished->value)->count());
    }

    public function test_seeding_twice_is_idempotent(): void
    {
        $counts = [User::count(), Enrollment::count(), AttendanceRecord::count(), FinalResult::count(), Announcement::count()];

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($counts, [User::count(), Enrollment::count(), AttendanceRecord::count(), FinalResult::count(), Announcement::count()]);
    }

    public function test_demo_accounts_and_roles(): void
    {
        $this->assertTrue(User::where('email', 'admin@example.com')->firstOrFail()->isAdmin());
        $this->assertTrue(User::where('email', 'gv1@example.com')->firstOrFail()->isLecturer());
        $this->assertTrue(User::where('email', 'sv12@example.com')->firstOrFail()->isStudent());
        $this->assertTrue(User::where('email', 'superadmin@example.com')->firstOrFail()->can(Permission::ROLES_MANAGE));
        $this->assertFalse(User::where('email', 'admin@example.com')->firstOrFail()->can(Permission::ROLES_MANAGE));
    }

    public function test_enrollment_counts_are_consistent_with_enrolled_count(): void
    {
        foreach (ClassSection::all() as $section) {
            $this->assertSame($section->enrollments()->active()->count(), $section->enrolled_count, "Lớp {$section->code}");
            $this->assertLessThanOrEqual($section->capacity, $section->enrolled_count);
        }
    }

    public function test_grade_weights_of_every_class_sum_to_100(): void
    {
        foreach (ClassSection::with('gradeComponents')->get() as $section) {
            $this->assertEqualsWithDelta(100.0, $section->gradeComponents->sum('weight'), 0.005, "Lớp {$section->code}");
        }
    }

    public function test_published_results_exist_and_student_gpa_is_computed(): void
    {
        $this->assertSame(24, FinalResult::count()); // 12 sinh vien x 2 lop hoc ky cu

        $student = Student::where('student_code', 'SV00001')->firstOrFail();
        $gpa = app(StudentGpaQuery::class)->cumulativeGpa($student);

        $this->assertNotNull($gpa);
        $this->assertGreaterThanOrEqual(0.0, $gpa);
        $this->assertLessThanOrEqual(4.0, $gpa);
    }

    public function test_attendance_summary_for_a_seeded_enrollment(): void
    {
        $enrollment = Enrollment::active()->firstOrFail();

        $summary = app(AttendanceService::class)->summaryForEnrollment($enrollment);

        $this->assertSame(3, $summary['held']);
        $this->assertSame($summary['absent'] / 3, $summary['rate']);
    }

    public function test_class_announcement_audience_is_registered_students_only(): void
    {
        $section = ClassSection::where('status', ClassSectionStatus::Open->value)->firstOrFail();
        $announcement = Announcement::where('class_section_id', $section->id)->firstOrFail();

        $audience = app(AnnouncementService::class)->resolveAudience($announcement);

        $this->assertCount(8, $audience); // 8 sinh vien dang ky hoc ky hien tai
        $this->assertTrue($audience->every(fn (User $u) => $u->isStudent()));
    }

    public function test_school_wide_announcement_reaches_every_active_user(): void
    {
        $announcement = Announcement::where('title', 'Chào mừng học kỳ mới')->firstOrFail();

        $audience = app(AnnouncementService::class)->resolveAudience($announcement);

        $this->assertCount(User::count(), $audience);
    }
}
