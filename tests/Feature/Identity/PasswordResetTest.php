<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\User;
use App\Modules\Identity\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_password_reset_request_form(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Quên mật khẩu')
            ->assertSee('login-card')
            ->assertSee('art-side');
    }

    public function test_login_page_links_to_password_reset_request(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('password.request'));
    }

    public function test_reset_link_is_sent_for_an_existing_account(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_unknown_email_gets_the_same_confirmation_without_a_notification(): void
    {
        Notification::fake();

        $response = $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'unknown@example.com'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status');

        $this->assertSame(
            'Nếu email thuộc một tài khoản trong hệ thống, hướng dẫn đặt lại mật khẩu sẽ được gửi đến hộp thư đó.',
            $response->getSession()->get('status'),
        );
        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('Đặt lại mật khẩu')
            ->assertSee('login-card')
            ->assertSee('art-side');

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-secure-password', $user->refresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_invalid_or_expired_token_does_not_change_the_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->from(route('password.reset', ['token' => 'invalid-token', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('old-password', $user->refresh()->password));
    }

    public function test_password_reset_requires_confirmation_and_minimum_length(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['password']);
    }
}
