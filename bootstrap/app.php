<?php

use App\Modules\Identity\Middleware\EnsureUserIsActive;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Route cua tung module duoc nap trong ModuleServiceProvider.
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Lop kiem tra 1 (muc 5.8): role / permission o tang route.
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            // Tai khoan bi khoa khong duoc tiep tuc dung phien dang nhap.
            'active' => EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Vi pham quy tac nghiep vu (BR-xx) -> thong bao than thien, khong 500.
        $exceptions->render(function (BusinessRuleException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'rule' => $e->rule,
                ], 422);
            }

            return back()->withInput()->withErrors(['business_rule' => $e->getMessage()]);
        });
    })->create();
