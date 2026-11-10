<?php

namespace Tests\Feature\Academic;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentCurriculumTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_cannot_access_curriculum(): void
    {
        $this->get('/student/curriculum')->assertRedirect('/login');
        $this->get('/student/courses')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_curriculum(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student/curriculum')->assertStatus(403);
        $this->actingAs($lecturer)->get('/student/courses')->assertStatus(403);
    }

    public function test_student_can_view_curriculum(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/curriculum');

        $response->assertStatus(200);
        $response->assertSee('Khung Chương Trình Đào Tạo');
        $response->assertSee('Kỹ thuật Phần mềm');
        $response->assertSee('Học kỳ 1');
        $response->assertSee('CS101');
        $response->assertSee('Nhập môn lập trình');
    }

    public function test_student_can_view_course_catalog(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/courses');

        $response->assertStatus(200);
        $response->assertSee('Tra Cứu Danh Mục Môn Học');
        $response->assertSee('CS101');
        $response->assertSee('Cấu trúc dữ liệu và giải thuật');
    }

    public function test_student_can_search_courses_by_keyword(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/courses?keyword=CS101');

        $response->assertStatus(200);
        $response->assertSee('CS101');
        $response->assertSee('Nhập môn lập trình');
    }

    public function test_student_can_view_course_detail(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/courses/CS102');

        $response->assertStatus(200);
        $response->assertSee('CS102');
        $response->assertSee('Kỹ thuật lập trình');
        $response->assertSee('Điều kiện tiên quyết');
        $response->assertSee('CS101');
    }

    public function test_nonexistent_course_redirects_with_error(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/courses/UNKNOWN999');

        $response->assertRedirect('/student/courses');
        $response->assertSessionHas('error');
    }
}

