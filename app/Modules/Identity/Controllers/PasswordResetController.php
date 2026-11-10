<?php

namespace App\Modules\Identity\Controllers;

use App\Modules\Identity\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('identity::auth.login', ['authMode' => 'forgot']);
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with(
            'status',
            'Nếu email thuộc một tài khoản trong hệ thống, hướng dẫn đặt lại mật khẩu sẽ được gửi đến hộp thư đó.',
        );
    }

    public function edit(Request $request, string $token): View
    {
        return view('identity::auth.login', [
            'authMode' => 'reset',
            'email' => $request->query('email', ''),
            'token' => $token,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Mật khẩu đã được đặt lại. Bạn có thể đăng nhập bằng mật khẩu mới.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu liên kết mới.']);
    }
}
