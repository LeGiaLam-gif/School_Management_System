<?php

namespace Tests\Feature\Enrollment;

use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Events\EnrollmentCreated;
use App\Modules\Enrollment\Events\EnrollmentDropped;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Services\EnrollmentService;
use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\CreatesRoles;
use Tests\TestCase;

/**
 * Luu y: SQLite bo qua lockForUpdate. Kiem thu dong thoi that (2 tien trinh cung dang ky
 * lop con 1 cho) chay tren MySQL o CI - xem docs/04-team/progress.md (Sprint 3).
 */
class EnrollmentServiceTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    private function openSection(int $capacity = 40, int $enrolled = 0): ClassSection
    {
        $semester = Semester::factory()->current()->registrationOpen()->create();

        return ClassSection::factory()
            ->for($semester)
            ->capacity($capacity, $enrolled)
            ->status(ClassSectionStatus::Open)
            ->create();
    }

    public function test_register_creates_enrollment_and_increments_count(): void
    {
        Event::fake([EnrollmentCreated::class]);
        $student = Student::factory()->create();
        $section = $this->openSection();

        $enrollment = app(EnrollmentService::class)->register($student, $section->id);

        $this->assertSame(EnrollmentStatus::Registered, $enrollment->status);
        $this->assertSame(1, $section->refresh()->enrolled_count);
        Event::assertDispatched(EnrollmentCreated::class);
    }

    public function test_register_rejects_full_class(): void
    {
        $student = Student::factory()->create();
        $section = $this->openSection(capacity: 1, enrolled: 1);

        try {
            app(EnrollmentService::class)->register($student, $section->id);
            $this->fail('Phải từ chối lớp đầy.');
        } catch (BusinessRuleException $e) {
            $this->assertSame('BR-05', $e->rule);
        }

        $this->assertSame(1, $section->refresh()->enrolled_count);
        $this->assertSame(0, Enrollment::count());
    }

    public function test_last_seat_can_be_taken_once(): void
    {
        $section = $this->openSection(capacity: 1, enrolled: 0);
        $service = app(EnrollmentService::class);

        $service->register(Student::factory()->create(), $section->id);

        $this->expectException(BusinessRuleException::class);
        $service->register(Student::factory()->create(), $section->id);
    }

    public function test_register_rejects_closed_registration_window(): void
    {
        $semester = Semester::factory()->current()->create([
            'registration_open_at' => now()->subDays(10),
            'registration_close_at' => now()->subDay(),
        ]);
        $section = ClassSection::factory()->for($semester)->create();

        $this->expectException(BusinessRuleException::class);
        app(EnrollmentService::class)->register(Student::factory()->create(), $section->id);
    }

    public function test_register_rejects_non_open_class(): void
    {
        $semester = Semester::factory()->current()->registrationOpen()->create();
        $section = ClassSection::factory()->for($semester)->status(ClassSectionStatus::Draft)->create();

        $this->expectException(BusinessRuleException::class);
        app(EnrollmentService::class)->register(Student::factory()->create(), $section->id);
    }

    public function test_duplicate_registration_is_rejected_and_does_not_change_count(): void
    {
        $student = Student::factory()->create();
        $section = $this->openSection();
        $service = app(EnrollmentService::class);

        $service->register($student, $section->id);

        try {
            $service->register($student, $section->id);
            $this->fail('Phải từ chối đăng ký trùng.');
        } catch (BusinessRuleException $e) {
            $this->assertSame('BR-01', $e->rule);
        }

        $this->assertSame(1, $section->refresh()->enrolled_count);
    }

    public function test_drop_frees_the_seat_and_keeps_the_record(): void
    {
        Event::fake([EnrollmentDropped::class]);
        $student = Student::factory()->create();
        $section = $this->openSection();
        $service = app(EnrollmentService::class);
        $enrollment = $service->register($student, $section->id);

        $service->drop($enrollment);

        $this->assertSame(0, $section->refresh()->enrolled_count);
        $this->assertSame(EnrollmentStatus::Dropped, $enrollment->refresh()->status);
        $this->assertNotNull($enrollment->dropped_at);
        $this->assertDatabaseCount('enrollments', 1); // khong xoa de truy vet
        Event::assertDispatched(EnrollmentDropped::class);
    }

    public function test_drop_after_deadline_is_rejected(): void
    {
        $student = Student::factory()->create();
        $section = $this->openSection();
        $enrollment = app(EnrollmentService::class)->register($student, $section->id);

        $section->semester->update(['drop_deadline' => now()->subDays(2)->toDateString()]);

        $this->expectException(BusinessRuleException::class);
        app(EnrollmentService::class)->drop($enrollment);
    }

    public function test_cannot_drop_twice(): void
    {
        $student = Student::factory()->create();
        $section = $this->openSection();
        $service = app(EnrollmentService::class);
        $enrollment = $service->register($student, $section->id);
        $service->drop($enrollment);

        $this->expectException(BusinessRuleException::class);
        $service->drop($enrollment);
    }
}
