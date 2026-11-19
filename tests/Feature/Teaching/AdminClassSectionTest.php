<?php

namespace Tests\Feature\Teaching;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Semester;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class AdminClassSectionTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    private User $admin;
    private Course $course;
    private Semester $semester;
    private Lecturer $lecturer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->admin = User::factory()->withRole(RoleName::Admin)->create();

        $dept = Department::factory()->create(['code' => 'CNTT']);
        $this->course = Course::factory()->create(['department_id' => $dept->id, 'code' => 'CS101']);
        $this->semester = Semester::factory()->create(['code' => '2026-HK1']);

        $lecUser = User::factory()->withRole(RoleName::Lecturer)->create(['name' => 'Thầy Hoàng']);
        $this->lecturer = Lecturer::factory()->create([
            'user_id' => $lecUser->id,
            'lecturer_code' => 'GV001',
        ]);
    }

    public function test_admin_can_view_class_sections_index(): void
    {
        ClassSection::factory()->create([
            'course_id' => $this->course->id,
            'semester_id' => $this->semester->id,
            'lecturer_id' => $this->lecturer->id,
            'code' => 'CS101.01',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.class-sections.index'));

        $response->assertOk()
            ->assertSee('Lớp học phần')
            ->assertSee('Giao diện cơ bản đã sẵn sàng')
            ->assertSee('CS101.01');
    }

    public function test_admin_can_create_a_class_section(): void
    {
        $payload = [
            'course_id' => $this->course->id,
            'semester_id' => $this->semester->id,
            'lecturer_id' => $this->lecturer->id,
            'code' => 'CS101.02',
            'capacity' => 60,
            'status' => ClassSectionStatus::Open->value,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.class-sections.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_sections', [
            'code' => 'CS101.02',
            'capacity' => 60,
            'course_id' => $this->course->id,
            'semester_id' => $this->semester->id,
            'lecturer_id' => $this->lecturer->id,
        ]);
    }

    public function test_admin_can_assign_lecturer_to_class_section(): void
    {
        $section = ClassSection::factory()->create([
            'course_id' => $this->course->id,
            'semester_id' => $this->semester->id,
            'lecturer_id' => null,
            'code' => 'CS101.03',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.class-sections.assign-lecturer', $section), [
                'lecturer_id' => $this->lecturer->id,
            ]);

        $response->assertRedirect();
        $this->assertSame($this->lecturer->id, $section->fresh()->lecturer_id);
    }
}
