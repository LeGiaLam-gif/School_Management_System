<?php

namespace Tests\Feature\Enrollment;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

/**
 * NFR-SEC-02: chan IDOR - sinh vien khong xem/huy dang ky cua nguoi khac.
 */
class EnrollmentPolicyTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_student_sees_and_drops_only_own_enrollment(): void
    {
        $mine = Student::factory()->create();
        $other = Student::factory()->create();
        $section = ClassSection::factory()->create();

        $own = Enrollment::factory()->create(['student_id' => $mine->id, 'class_section_id' => $section->id]);
        $foreign = Enrollment::factory()->create(['student_id' => $other->id, 'class_section_id' => $section->id]);

        $user = $mine->user;

        $this->assertTrue($user->can('view', $own));
        $this->assertTrue($user->can('drop', $own));
        $this->assertFalse($user->can('view', $foreign));
        $this->assertFalse($user->can('drop', $foreign));
    }

    public function test_lecturer_sees_enrollments_only_of_own_classes(): void
    {
        $lecturer = Lecturer::factory()->create();
        $mySection = ClassSection::factory()->create(['lecturer_id' => $lecturer->id]);
        $otherSection = ClassSection::factory()->create();

        $inMine = Enrollment::factory()->create(['class_section_id' => $mySection->id]);
        $inOther = Enrollment::factory()->create(['class_section_id' => $otherSection->id]);

        $this->assertTrue($lecturer->user->can('view', $inMine));
        $this->assertFalse($lecturer->user->can('view', $inOther));
        $this->assertFalse($lecturer->user->can('drop', $inMine));
    }

    public function test_admin_can_view_and_drop_any_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();

        $this->assertTrue($admin->can('view', $enrollment));
        $this->assertTrue($admin->can('drop', $enrollment));
    }
}
