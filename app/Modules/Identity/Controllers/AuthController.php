<?php

namespace App\Modules\Identity\Controllers;

use App\Modules\Identity\Requests\LoginRequest;
use App\Modules\Identity\Services\AuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller mong: nhan request -> goi Service -> tra response (muc 5.2).
 */
class AuthController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function showLogin(): View
    {
        return view('identity::auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $this->auth->attempt(
            $request->string('login')->toString(),
            $request->string('password')->toString(),
            $request->boolean('remember'),
            (string) $request->ip(),
        );

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
