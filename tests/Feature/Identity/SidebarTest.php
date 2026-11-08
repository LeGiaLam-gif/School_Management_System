<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class SidebarTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_sidebar_shows_dashboard_for_current_role_and_profile_link(): void
    {
        $dashboards = [
            RoleName::Admin->value => ['route' => 'admin.home', 'label' => 'Trang quản trị'],
            RoleName::Lecturer->value => ['route' => 'lecturer.home', 'label' => 'Trang giảng viên'],
            RoleName::Student->value => ['route' => 'student.home', 'label' => 'Trang sinh viên'],
        ];

        foreach ($dashboards as $role => $dashboard) {
            $user = User::factory()->withRole(RoleName::from($role))->create();

            $response = $this->actingAs($user)
                ->get(route($dashboard['route']))
                ->assertOk()
                ->assertSee($dashboard['label'])
                ->assertSee('aria-label="Ẩn hoặc hiện thanh điều hướng"', false)
                ->assertSee('images/logo.png');

            preg_match('/<nav class="sms-sidebar-nav".*?<\/nav>/s', $response->getContent(), $matches);
            $sidebar = $matches[0] ?? '';
            $profileRoute = $role === RoleName::Student->value ? 'student.profile' : 'profile.show';

            $this->assertNotSame('', $sidebar);
            $this->assertSame(1, substr_count($sidebar, route($profileRoute)));
        }
    }

    public function test_student_sidebar_shows_student_navigation_with_unavailable_modules_disabled(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)
            ->get(route('student.home'))
            ->assertOk()
            ->assertSee('Hồ sơ cá nhân');

        preg_match('/<nav class="sms-sidebar-nav".*?<\/nav>/s', $response->getContent(), $matches);
        $sidebar = $matches[0] ?? '';

        $this->assertNotSame('', $sidebar);
        $this->assertStringContainsString(route('student.profile'), $sidebar);

        foreach (['Chương trình đào tạo', 'Đăng ký học phần', 'Thời khóa biểu', 'Điểm &amp; GPA', 'Chuyên cần', 'Thông báo'] as $label) {
            $this->assertStringContainsString($label, $sidebar);
        }

        $this->assertStringContainsString('class="sms-sidebar-link sms-sidebar-link-disabled" aria-disabled="true"', $sidebar);
        $this->assertStringNotContainsString('href="#"', $sidebar);
    }

    public function test_student_dashboard_uses_the_shared_welcome_design_and_student_details(): void
    {
        $student = User::factory()->student()->create([
            'name' => 'Nguyen Thi Mai',
            'code' => 'SV00002',
        ]);

        $this->actingAs($student)
            ->get(route('student.home'))
            ->assertOk()
            ->assertSee('class="sms-welcome-card"', false)
            ->assertSee('Chào mừng, Nguyen Thi Mai')
            ->assertSee('SV00002')
            ->assertSee('HỆ THỐNG QUẢN LÝ ĐÀO TẠO')
            ->assertSee('Khóa tuyển sinh')
            ->assertSee('Ngành học')
            ->assertSee('Trạng thái')
            ->assertSee('Điểm', false)
            ->assertSee('Thời khóa biểu', false)
            ->assertSee('Chức năng chưa khả dụng')
            ->assertDontSee('>Học vụ<', false)
            ->assertDontSee('>Kết quả<', false)
            ->assertDontSee('href="#"', false)
            ->assertDontSee('btn-outline-success', false)
            ->assertDontSee('Xem kết quả học tập')
            ->assertDontSee('Đăng ký môn học')
            ->assertSee(route('student.profile'));
    }

    public function test_non_student_sidebar_does_not_show_student_navigation(): void
    {
        $lecturer = User::factory()->lecturer()->create();

        $response = $this->actingAs($lecturer)
            ->get(route('lecturer.home'))
            ->assertOk()
            ->assertSee(route('profile.show'));

        preg_match('/<nav class="sms-sidebar-nav".*?<\/nav>/s', $response->getContent(), $matches);
        $sidebar = $matches[0] ?? '';

        $this->assertNotSame('', $sidebar);
        $this->assertStringNotContainsString(route('student.profile'), $sidebar);
        $this->assertStringNotContainsString('Đăng ký học phần', $sidebar);
        $this->assertStringNotContainsString('Thời khóa biểu', $sidebar);
    }

    public function test_lecturer_sidebar_shows_documented_navigation_as_unavailable_until_routes_exist(): void
    {
        $lecturer = User::factory()->lecturer()->create();

        $response = $this->actingAs($lecturer)
            ->get(route('lecturer.home'))
            ->assertOk();

        preg_match('/<nav class="sms-sidebar-nav".*?<\/nav>/s', $response->getContent(), $matches);
        $sidebar = $matches[0] ?? '';

        $this->assertNotSame('', $sidebar);
        $this->assertStringContainsString(route('profile.show'), $sidebar);

        foreach ([
            'Lớp được phân công',
            'Danh sách sinh viên',
            'Lịch dạy',
            'Điểm danh',
            'Quản lý điểm',
            'Thông báo',
            'Báo cáo &amp; xuất dữ liệu',
        ] as $label) {
            $this->assertStringContainsString($label, $sidebar);
        }

        $this->assertSame(7, substr_count($sidebar, 'class="sms-sidebar-link sms-sidebar-link-disabled"'));
        $this->assertStringNotContainsString('href="#"', $sidebar);
        $this->assertStringNotContainsString('Chương trình đào tạo', $sidebar);
    }

    public function test_lecturer_dashboard_shows_documented_actions_until_routes_are_available(): void
    {
        $lecturer = User::factory()->lecturer()->create();

        $this->actingAs($lecturer)
            ->get(route('lecturer.home'))
            ->assertOk()
            ->assertSee('class="sms-lecturer-action-grid"', false)
            ->assertSee('Hồ sơ cá nhân')
            ->assertSee('Lớp được phân công')
            ->assertSee('Danh sách sinh viên')
            ->assertSee('Lịch dạy')
            ->assertSee('Điểm danh')
            ->assertSee('Quản lý điểm')
            ->assertSee('Thông báo')
            ->assertSee('Báo cáo', false)
            ->assertSee('xuất dữ liệu')
            ->assertSee('sms-lecturer-action-icon', false)
            ->assertSee(route('profile.show'))
            ->assertDontSee('Các nội dung học vụ sẽ được bổ sung')
            ->assertDontSee('Xem các lớp học phần')
            ->assertDontSee('Tra cứu danh sách sinh viên')
            ->assertDontSee('href="#"', false);
    }

    public function test_guest_does_not_see_authenticated_sidebar(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Điều hướng chính')
            ->assertDontSee('Trang quản trị');
    }
}
