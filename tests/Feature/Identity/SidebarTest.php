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
                ->assertSee('images/logo.png')
                ->assertSee('Hồ sơ cá nhân');

            $this->assertSame(1, substr_count($response->getContent(), route('profile.show')));
        }
    }

    public function test_admin_dashboard_shows_management_cards_and_disabled_navigation(): void
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.home'))
            ->assertOk()
            ->assertSee('Bảng điều khiển')
            ->assertSee('class="sms-admin-action-grid"', false)
            ->assertSee('Quản lý người dùng')
            ->assertSee('Quản lý khoa')
            ->assertSee('Quản lý ngành')
            ->assertSee('Quản lý lớp')
            ->assertSee('Quản lý học phần')
            ->assertSee('Quản lý học kỳ')
            ->assertSee('Lớp học phần')
            ->assertSee('Thống kê &amp; báo cáo', false)
            ->assertDontSee('href="#"', false);

        $this->assertSame(8, substr_count($response->getContent(), 'class="sms-admin-action-card'));
        $this->assertSame(16, substr_count($response->getContent(), 'aria-disabled="true"'));

        preg_match('/<nav class="sms-sidebar-nav".*?<\/nav>/s', $response->getContent(), $matches);
        $sidebar = $matches[0] ?? '';

        foreach (['Người dùng', 'Khoa', 'Ngành', 'Lớp', 'Học phần', 'Học kỳ', 'Lớp học phần', 'Thống kê &amp; báo cáo'] as $label) {
            $this->assertStringContainsString($label, $sidebar);
        }

        $this->assertSame(8, substr_count($sidebar, 'class="sms-sidebar-link sms-sidebar-link-disabled" aria-disabled="true"'));
    }

    public function test_guest_does_not_see_authenticated_sidebar(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Điều hướng chính')
            ->assertDontSee('Trang quản trị');
    }
}
