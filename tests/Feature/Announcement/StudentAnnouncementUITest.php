<?php

namespace Tests\Feature\Announcement;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentAnnouncementUITest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_cannot_access_announcements(): void
    {
        $this->get('/student/announcements')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_announcements(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student/announcements')->assertStatus(403);
    }

    public function test_student_can_view_announcements_page(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/announcements');

        $response->assertStatus(200);
        $response->assertSee('Hộp Thông Báo Sinh Viên');
        $response->assertSee('Kế hoạch Đăng ký học phần');
    }

    public function test_student_can_view_announcement_detail(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/announcements/1');

        $response->assertStatus(200);
        $response->assertSee('Kế hoạch Đăng ký học phần');
        $response->assertSee('Phòng Đào tạo & Quản lý Đào tạo');
    }

    public function test_student_redirected_on_nonexistent_announcement(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/announcements/99999');

        $response->assertRedirect('/student/announcements');
        $response->assertSessionHas('error');
    }
}
