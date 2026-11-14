<?php

namespace Tests\Feature\Enrollment;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentEnrollmentUITest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_cannot_access_enrollment(): void
    {
        $this->get('/student/enrollment')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_enrollment(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student/enrollment')->assertStatus(403);
    }

    public function test_student_can_view_enrollment_page(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/enrollment');

        $response->assertStatus(200);
        $response->assertSee('Đăng Ký Học Phần');
        $response->assertSee('Học kỳ 1 năm học 2026 - 2027');
        $response->assertSee('Lớp học phần đã đăng ký');
        $response->assertSee('Danh sách lớp học phần mở trong kỳ');
    }

    public function test_student_can_register_open_class(): void
    {
        $student = User::factory()->student()->create();

        // Register class CS201.O11 (not in default enrolled session)
        $response = $this->actingAs($student)->post('/student/enrollment/register', [
            'class_section_id' => 'CS201.O11',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_student_cannot_register_already_enrolled_class(): void
    {
        $student = User::factory()->student()->create();

        // Default session has CS102.O11 enrolled
        $response = $this->actingAs($student)->post('/student/enrollment/register', [
            'class_section_id' => 'CS102.O11',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_student_can_drop_enrolled_class(): void
    {
        $student = User::factory()->student()->create();

        // Drop CS102.O11
        $response = $this->actingAs($student)->post('/student/enrollment/drop', [
            'class_section_id' => 'CS102.O11',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}

