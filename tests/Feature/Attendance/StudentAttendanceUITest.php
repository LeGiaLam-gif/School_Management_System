<?php

namespace Tests\Feature\Attendance;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentAttendanceUITest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_cannot_access_attendance(): void
    {
        $this->get('/student/attendance')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_attendance(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student/attendance')->assertStatus(403);
    }

    public function test_student_can_view_attendance_page(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/attendance');

        $response->assertStatus(200);
        $response->assertSee('Theo Dõi Điểm Danh & Chuyên Cần');
        $response->assertSee('Kỹ thuật lập trình');
        $response->assertSee('Nhập môn Công nghệ phần mềm');
        $response->assertSee('Đủ điều kiện dự thi');
    }
}
