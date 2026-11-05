<?php

namespace App\Modules\Identity\Controllers;

use App\Shared\Enums\RoleName;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Trang chu theo vai tro. Dashboard day du (FR-RPT-01) thuoc module Reporting (Phase 2);
 * o day chi dieu huong va hien khung trang de kiem tra phan quyen.
 */
class DashboardController extends Controller
{
    public function home(Request $request): RedirectResponse
    {
        $user = $request->user();

        return match (true) {
            $user->isAdmin() => redirect()->route('admin.home'),
            $user->isLecturer() => redirect()->route('lecturer.home'),
            $user->isStudent() => redirect()->route('student.home'),
            default => abort(403, 'Tài khoản chưa được gán vai trò.'),
        };
    }

    public function admin(): View
    {
        return $this->page(RoleName::Admin);
    }

    public function lecturer(): View
    {
        return $this->page(RoleName::Lecturer);
    }

    public function student(): View
    {
        return $this->page(RoleName::Student);
    }

    private function page(RoleName $role): View
    {
        return view('identity::dashboard', ['role' => $role]);
    }
}
