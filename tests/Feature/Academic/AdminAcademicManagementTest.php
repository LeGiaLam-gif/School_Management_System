<?php

namespace Tests\Feature\Academic;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Semester;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class AdminAcademicManagementTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    private User $admin;

    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $this->admin = User::factory()->withRole(RoleName::Admin)->create();
        $this->department = Department::factory()->create([
            'code' => 'CNTT',
            'name' => 'Công nghệ Thông tin',
        ]);
    }

    public function test_admin_can_view_courses_index(): void
    {
        Course::factory()->count(3)->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.courses.index'));

        $response->assertOk()
            ->assertSee('Quản lý học phần')
            ->assertSee('Giao diện cơ bản đã sẵn sàng');
    }

    public function test_admin_can_create_a_course(): void
    {
        $payload = [
            'department_id' => $this->department->id,
            'code' => 'CS102',
            'name' => 'Cấu trúc dữ liệu và giải thuật',
            'credits' => 4,
            'theory_periods' => 45,
            'practice_periods' => 30,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.courses.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'code' => 'CS102',
            'name' => 'Cấu trúc dữ liệu và giải thuật',
            'credits' => 4,
        ]);
    }

    public function test_admin_can_view_semesters_index(): void
    {
        Semester::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.semesters.index'));

        $response->assertOk()
            ->assertSee('Quản lý học kỳ')
            ->assertSee('Giao diện cơ bản đã sẵn sàng');
    }

    public function test_admin_can_create_a_semester(): void
    {
        $payload = [
            'code' => '2026-HK3',
            'name' => 'Học kỳ hè',
            'academic_year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2026-08-15',
            'min_credits' => 3,
            'max_credits' => 12,
            'is_current' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.semesters.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('semesters', [
            'code' => '2026-HK3',
            'name' => 'Học kỳ hè',
            'is_current' => true,
        ]);
    }

    public function test_admin_can_set_current_semester(): void
    {
        $sem1 = Semester::factory()->create(['is_current' => true]);
        $sem2 = Semester::factory()->create(['is_current' => false]);

        $response = $this->actingAs($this->admin)->post(route('admin.semesters.set-current', $sem2));

        $response->assertRedirect();
        $this->assertTrue($sem2->fresh()->is_current);
        $this->assertFalse($sem1->fresh()->is_current);
    }
}
