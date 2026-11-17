<?php

namespace App\Modules\Identity\Middleware;

use App\Modules\Identity\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Đổi mật khẩu khi đăng nhập lần đầu
 */
class EnsurePasswordHasBeenChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && $user->isActive() && $user->must_change_password
            && ! $request->routeIs('password.first.edit', 'profile.password', 'logout')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bạn cần đổi mật khẩu trước khi tiếp tục.'], 403);
            }

            return redirect()->route('password.first.edit');
        }

        return $next($request);
    }
}
