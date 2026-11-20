<?php

namespace App\Modules\Identity\Controllers;

use App\Modules\Identity\Models\User;
use App\Modules\Identity\Requests\ChangePasswordRequest;
use App\Modules\Identity\Requests\UpdateContactRequest;
use App\Modules\Identity\Support\IntendedPath;
use App\Shared\Enums\RoleName;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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
            static function (string $role): string {
                $enum = RoleName::tryFrom($role);

                return $enum instanceof RoleName ? $enum->label() : $role;
            },
        );

        return view('identity::profile.show', compact('user', 'roles'));
    }

    public function showFirstPasswordChange(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->must_change_password) {
            return redirect()->route('home');
        }

        return view('identity::auth.first-password-change', ['hideAppShell' => true]);
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        $firstChange = $user->must_change_password;

        $user->forceFill([
            'password' => $request->string('new_password')->toString(),
            'must_change_password' => false,
        ])->save();

        if ($firstChange) {
            $request->session()->regenerate();

            return redirect(IntendedPath::pull($request))->with('success', 'Đổi mật khẩu thành công.');
        }

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }

    public function updateContact(UpdateContactRequest $request): RedirectResponse
    {
        $user = $request->user();
        $contact = $request->validated();

        abort_unless($user instanceof User, 403);

        if ($user->isStudent()) {
            $user->student()->firstOrFail()->update($contact);
        } elseif ($user->isLecturer()) {
            $user->lecturer()->firstOrFail()->update($contact);
        } elseif ($user->isAdmin()) {
            $user->forceFill($contact)->save();
        } else {
            abort(403);
        }

        return back()->with('success', 'Cập nhật thông tin liên hệ thành công.');
    }
}
