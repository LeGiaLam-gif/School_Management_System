<?php

namespace Tests\Feature\Grading;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentGradeUITest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_cannot_access_grades(): void
    {
        $this->get('/student/grades')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_grades(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student/grades')->assertStatus(403);
    }

    public function test_student_can_view_grades_page(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/grades');

        $response->assertStatus(200);
        $response->assertSee('Bảng Điểm & Điểm Trung Bình (GPA)');
        $response->assertSee('GPA Tích lũy (Hệ 4)');
        $response->assertSee('Nhập môn lập trình');
        $response->assertSee('CS101');
        $response->assertSee('Quy chế Thang điểm');
    }

    public function test_student_can_filter_by_semester(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/grades?semester=HK1-2024');

        $response->assertStatus(200);
        $response->assertSee('Học kỳ 1 năm học 2024 - 2025');
    }
}

