<?php

namespace App\Modules\Teaching\Policies;

use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Authorization\Permission;

/**
 * Lecturer chi thao tac tren lop co lecturer_id la minh; Student chi thay lop dang mo (muc 2.5).
 */
class ClassSectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::CLASS_SECTIONS_VIEW);
    }

    public function view(User $user, ClassSection $section): bool
    {
        if ($user->can(Permission::CLASS_SECTIONS_MANAGE)) {
            return true;
        }

        if ($user->isLecturer()) {
            return $section->isTaughtBy($user);
        }

        return $user->can(Permission::CLASS_SECTIONS_VIEW) && $section->status->acceptsEnrollment();
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CLASS_SECTIONS_MANAGE);
    }

    public function update(User $user, ClassSection $section): bool
    {
        return $user->can(Permission::CLASS_SECTIONS_MANAGE);
    }

    public function delete(User $user, ClassSection $section): bool
    {
        return $user->can(Permission::CLASS_SECTIONS_MANAGE);
    }
}
