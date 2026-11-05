<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\CreatesRoles;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_login_page_is_shown_to_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Đăng nhập');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::factory()->student()->create(['email' => 'a@example.com']);

        $this->post('/login', ['login' => 'a@example.com', 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->refresh()->last_login_at);
    }

    public function test_user_can_login_with_code(): void
    {
        $user = User::factory()->lecturer()->create(['code' => 'GV001']);

        $this->post('/login', ['login' => 'GV001', 'password' => 'password'])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->student()->create(['email' => 'a@example.com']);

        $this->from('/login')
            ->post('/login', ['login' => 'a@example.com', 'password' => 'sai'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_unknown_account_gets_same_error_as_wrong_password(): void
    {
        $this->from('/login')
            ->post('/login', ['login' => 'nobody@example.com', 'password' => 'password'])
            ->assertSessionHasErrors(['login' => trans('auth.failed')]);
    }

    public function test_locked_account_cannot_login(): void
    {
        User::factory()->student()->locked()->create(['email' => 'a@example.com']);

        $this->from('/login')
            ->post('/login', ['login' => 'a@example.com', 'password' => 'password'])
            ->assertSessionHasErrors(['login' => trans('auth.locked')]);

        $this->assertGuest();
    }

    public function test_locked_user_with_open_session_is_logged_out(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user);
        $user->update(['status' => 'locked']);

        $this->get('/student')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_too_many_failed_attempts_throttle_even_correct_password(): void
    {
        User::factory()->student()->create(['email' => 'a@example.com']);
        RateLimiter::clear('a@example.com|127.0.0.1');

        for ($i = 0; $i < config('sms.auth.max_attempts'); $i++) {
            $this->post('/login', ['login' => 'a@example.com', 'password' => 'sai']);
        }

        $this->post('/login', ['login' => 'a@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_login_requires_both_fields(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['login', 'password']);
    }

    public function test_user_can_logout(): void
    {
        $this->actingAs(User::factory()->student()->create());

        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_home_redirects_by_role(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/')->assertRedirect('/admin');
        $this->actingAs(User::factory()->lecturer()->create())->get('/')->assertRedirect('/lecturer');
        $this->actingAs(User::factory()->student()->create())->get('/')->assertRedirect('/student');
    }

    public function test_user_without_role_is_forbidden_from_home(): void
    {
        $this->actingAs(User::factory()->create())->get('/')->assertForbidden();
    }
}
