<?php

namespace App\Modules\Identity\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tai khoan bi khoa sau khi da dang nhap thi bi dang xuat o request ke tiep.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ! $user->isActive()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['login' => trans('auth.locked')]);
        }

        return $next($request);
    }
}
