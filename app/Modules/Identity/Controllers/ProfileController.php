<?php

namespace App\Modules\Identity\Controllers;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        $user->load(['student.program.department', 'lecturer.department']);
        $roles = $user->getRoleNames()->map(
            static fn (string $role): string => RoleName::tryFrom($role)?->label() ?? $role,
        );

        return view('identity::profile.show', compact('user', 'roles'));
    }
}
