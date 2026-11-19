<?php

namespace Tests\Feature\Reporting;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();

        $this->admin = User::factory()->withRole(RoleName::Admin)->create();
    }

    public function test_admin_can_view_reports_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertOk()
            ->assertSee('Thống kê &amp; báo cáo', false)
            ->assertSee('Giao diện cơ bản đã sẵn sàng');
    }

    public function test_admin_can_export_reports_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export', ['type' => 'class_sections']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }
}
