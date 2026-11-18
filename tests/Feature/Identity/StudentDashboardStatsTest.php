<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentDashboardStatsTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_is_redirected_to_login_from_student_dashboard(): void
    {
        $this->get('/student')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_dashboard(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student')->assertStatus(403);
    }

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $this->actingAs($admin)->get('/student')->assertStatus(403);
    }

    public function test_student_can_view_dashboard_with_comprehensive_statistics(): void
    {
        /** @var Student $student */
        $student = Student::factory()->create();
        $user = $student->user;

        $response = $this->actingAs($user)->get('/student');

        $response->assertStatus(200);

        // 1. Thong tin sinh vien & chao mung
        $response->assertSee('HEXA TECH ACADEMY');
        $response->assertSee('Chào mừng trở lại');
        $response->assertSee($user->name);
        $response->assertSee($student->student_code);

        // 2. The Thong ke trong yeu (Stat Cards)
        // Tien do tin chi
        $response->assertSee('Tiến độ tín chỉ');
        $response->assertSee('TC tích lũy');
        // GPA
        $response->assertSee('Điểm GPA tích lũy');
        $response->assertSee('Xếp loại học lực');
        // Tinh trang hoc vu (Academic Warning TT08/2021)
        $response->assertSee('Tình trạng học vụ');
        $response->assertSee('Thông tư 08/2021/TT-BGDĐT');
        // Chuyen can
        $response->assertSee('Tỷ lệ chuyên cần');
        $response->assertSee('tham gia lớp học');

        // 3. Lich hoc trong ngay
        $response->assertSee('Lịch học trong ngày');
        $response->assertSee(route('student.timetable'));

        // 4. Tien do CTDT & Lop sinh hoat
        $response->assertSee('Tiến độ tích lũy CTĐT');
        $response->assertSee(route('student.curriculum'));
        $response->assertSee('Lớp sinh hoạt &amp; Cố vấn', false);
        $response->assertSee('Cố vấn học tập (CVHT)');

        // 5. Thong bao moi nhat
        $response->assertSee('Thông báo mới');
        $response->assertSee(route('student.announcements'));

        // 6. 7 Loi tat chuc nang day du
        $response->assertSee(route('student.enrollment'));
        $response->assertSee(route('student.timetable'));
        $response->assertSee(route('student.grades'));
        $response->assertSee(route('student.attendance'));
        $response->assertSee(route('student.curriculum'));
        $response->assertSee(route('student.announcements'));
        $response->assertSee(route('student.profile'));
    }
}
