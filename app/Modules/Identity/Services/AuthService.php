<?php

namespace App\Modules\Identity\Services;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Xac thuc nguoi dung (FR-AUTH-01, FR-AUTH-05). Khong biet gi ve HTTP:
 * controller truyen vao login/password/ip va tu xu ly session + redirect.
 */
class AuthService
{
    /**
     * Dang nhap bang email HOAC ma so + mat khau.
     *
     * @throws ValidationException khi sai thong tin, tai khoan bi khoa hoac bi chan do nhap sai nhieu lan
     */
    public function attempt(string $login, string $password, bool $remember, string $ip): User
    {
        $key = $this->throttleKey($login, $ip);

        if (RateLimiter::tooManyAttempts($key, config('sms.auth.max_attempts'))) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'login' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => (int) ceil($seconds / 60),
                ]),
            ]);
        }

        $user = User::query()
            ->where($this->identifierColumn($login), $login)
            ->first();

        if ($user === null || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($key, config('sms.auth.decay_seconds'));

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        if ($user->status === UserStatus::Locked) {
            throw ValidationException::withMessages([
                'login' => trans('auth.locked'),
            ]);
        }

        RateLimiter::clear($key);

        Auth::login($user, $remember);

        $user->forceFill(['last_login_at' => now()])->save();

        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    private function identifierColumn(string $login): string
    {
        return filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'code';
    }

    private function throttleKey(string $login, string $ip): string
    {
        return Str::transliterate(Str::lower($login).'|'.$ip);
    }
}
