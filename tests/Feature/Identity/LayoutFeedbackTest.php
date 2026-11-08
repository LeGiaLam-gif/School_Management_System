<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\CreatesRoles;
use Tests\TestCase;

class LayoutFeedbackTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_authenticated_layout_displays_flash_messages_and_validation_errors(): void
    {
        $user = User::factory()->student()->create();
        $errors = (new ViewErrorBag)->put('default', new MessageBag(['name' => 'Tên là bắt buộc.']));

        $this->actingAs($user)
            ->withSession([
                'success' => 'Thao tác đã hoàn tất.',
                'status' => 'Thông tin đã được gửi.',
                'error' => 'Không thể hoàn tất thao tác.',
                'errors' => $errors,
            ])
            ->get(route('student.home'))
            ->assertOk()
            ->assertSee('Thao tác đã hoàn tất.')
            ->assertSee('Thông tin đã được gửi.')
            ->assertSee('Không thể hoàn tất thao tác.')
            ->assertSee('Tên là bắt buộc.')
            ->assertSee('role="alert"', false)
            ->assertSee('role="status"', false);
    }

    public function test_login_page_keeps_its_inline_validation_messages_without_duplicate_alert(): void
    {
        $errors = (new ViewErrorBag)->put('default', new MessageBag(['login' => 'Thông tin đăng nhập không hợp lệ.']));

        $this->withSession(['errors' => $errors])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('Thông tin đăng nhập không hợp lệ.')
            ->assertDontSee('role="alert"', false);
    }
}
