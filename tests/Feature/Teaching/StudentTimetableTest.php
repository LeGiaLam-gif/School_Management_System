<?php

namespace Tests\Feature\Teaching;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentTimetableTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_cannot_access_timetable(): void
    {
        $this->get('/student/timetable')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_timetable(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student/timetable')->assertStatus(403);
    }

    public function test_student_can_view_timetable_page(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/timetable');

        $response->assertStatus(200);
        $response->assertSee('Thời Khóa Biểu Sinh Viên');
        $response->assertSee('Thứ Hai');
        $response->assertSee('Thứ Sáu');
        $response->assertSee('Sáng (Tiết 1 - 3)');
    }

    public function test_student_can_navigate_weeks(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/timetable?week=1');

        $response->assertStatus(200);
        $response->assertSee('Thời Khóa Biểu Sinh Viên');
    }
}
