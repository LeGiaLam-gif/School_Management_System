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
            RoleName::Admin->value => ['route' => 'admin.home', 'label' => 'Trang quản trị', 'profile' => 'profile.show'],
            RoleName::Lecturer->value => ['route' => 'lecturer.home', 'label' => 'Trang giảng viên', 'profile' => 'profile.show'],
            RoleName::Student->value => ['route' => 'student.home', 'label' => 'Trang sinh viên', 'profile' => 'student.profile'],
        ];

        foreach ($dashboards as $role => $dashboard) {
            $user = User::factory()->withRole(RoleName::from($role))->create();

            $response = $this->actingAs($user)
                ->get(route($dashboard['route']))
                ->assertOk()
                ->assertSee($dashboard['label'])
                ->assertSee('aria-label="Ẩn hoặc hiện thanh điều hướng"', false)
                ->assertSee('images/logo.png')
                ->assertSee('Hồ sơ cá nhân');

            preg_match('/<nav class="sms-sidebar-nav".*?<\/nav>/s', $response->getContent(), $matches);
            $sidebar = $matches[0] ?? '';

            $this->assertNotSame('', $sidebar);
            $this->assertSame(1, substr_count($sidebar, route($dashboard['profile'])));
        }
    }

    public function test_admin_dashboard_links_to_the_management_workspaces(): void
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.home'))
            ->assertOk()
            ->assertSee('Bảng điều khiển')
            ->assertSee('class="sms-admin-action-grid"', false)
            ->assertSee('Quản lý người dùng')
            ->assertSee('Quản lý khoa')
            ->assertSee('Chương trình đào tạo')
            ->assertSee('Quản lý học phần')
            ->assertSee('Quản lý học kỳ')
            ->assertSee('Lớp học phần')
            ->assertSee('Thống kê &amp; báo cáo', false)
            ->assertDontSee('href="#"', false);

        $this->assertSame(7, substr_count($response->getContent(), 'class="sms-admin-action-card'));
        $this->assertSame(0, substr_count($response->getContent(), 'aria-disabled="true"'));

        preg_match('/<nav class="sms-sidebar-nav".*?<\/nav>/s', $response->getContent(), $matches);
        $sidebar = $matches[0] ?? '';

        foreach (['Người dùng', 'Khoa', 'Chương trình đào tạo', 'Học phần', 'Học kỳ', 'Lớp học phần', 'Thống kê &amp; báo cáo'] as $label) {
            $this->assertStringContainsString($label, $sidebar);
        }

        $this->assertSame(0, substr_count($sidebar, 'class="sms-sidebar-link sms-sidebar-link-disabled"'));
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

        $response = $this->actingAs($student)
            ->get(route('student.home'))
            ->assertOk()
            ->assertSee('class="sms-welcome-card"', false)
            ->assertSee('Chào mừng, Nguyen Thi Mai')
            ->assertSee('SV00002')
            ->assertSee('HỆ THỐNG QUẢN LÝ ĐÀO TẠO')
            ->assertSee('Khóa tuyển sinh')
            ->assertSee('Ngành học')
            ->assertSee('Trạng thái')
            ->assertSee('class="sms-student-action-grid"', false)
            ->assertSee('Điểm &amp; GPA', false)
            ->assertSee('Thời khóa biểu')
            ->assertSee('Chuyên cần')
            ->assertSee('Thông báo')
            ->assertDontSee('>Học vụ<', false)
            ->assertDontSee('>Kết quả<', false)
            ->assertDontSee('href="#"', false)
            ->assertDontSee('Theo dõi lịch học', false)
            ->assertDontSee('Xem thông tin sinh viên', false)
            ->assertSee(route('student.profile'));

        $this->assertSame(7, substr_count($response->getContent(), 'class="sms-student-action-card'));
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

    public function test_lecturer_sidebar_links_to_the_lecturer_workspaces(): void
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

        $this->assertSame(0, substr_count($sidebar, 'class="sms-sidebar-link sms-sidebar-link-disabled"'));
        $this->assertStringNotContainsString('href="#"', $sidebar);
        $this->assertStringNotContainsString('Chương trình đào tạo', $sidebar);
    }

    public function test_lecturer_dashboard_links_to_lecturer_workspaces(): void
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

    public function test_admin_and_lecturer_workspace_pages_are_visible_only_to_their_role(): void
    {
        $adminRoutes = [
            'admin.users.index' => 'Quản lý người dùng',
            'admin.departments.index' => 'Quản lý khoa',
            'admin.programs.index' => 'Chương trình đào tạo',
            'admin.courses.index' => 'Quản lý học phần',
            'admin.semesters.index' => 'Quản lý học kỳ',
            'admin.class-sections.index' => 'Lớp học phần',
            'admin.reports.index' => 'Thống kê & báo cáo',
        ];
        $lecturerRoutes = [
            'lecturer.classes' => 'Lớp được phân công',
            'lecturer.students' => 'Danh sách sinh viên',
            'lecturer.timetable' => 'Lịch dạy',
            'lecturer.attendance' => 'Điểm danh',
            'lecturer.grades' => 'Quản lý điểm',
            'lecturer.announcements' => 'Thông báo',
            'lecturer.reports' => 'Báo cáo & xuất dữ liệu',
        ];

        $admin = User::factory()->admin()->create();
        $lecturer = User::factory()->lecturer()->create();

        foreach ($adminRoutes as $route => $title) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk()
                ->assertSee($title)
                ->assertSee('Giao diện cơ bản đã sẵn sàng');

            $this->actingAs($lecturer)->get(route($route))->assertForbidden();
        }

        foreach ($lecturerRoutes as $route => $title) {
            $this->actingAs($lecturer)
                ->get(route($route))
                ->assertOk()
                ->assertSee($title)
                ->assertSee('Giao diện cơ bản đã sẵn sàng');

            $this->actingAs($admin)->get(route($route))->assertForbidden();
        }
    }

    public function test_guest_does_not_see_authenticated_sidebar(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Điều hướng chính')
            ->assertDontSee('Trang quản trị');
    }
}
