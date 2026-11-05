<?php

namespace App\Modules\Grading\Policies;

use App\Modules\Grading\Models\FinalResult;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Authorization\Permission;
use App\Shared\Enums\ClassSectionStatus;

/**
 * Giang vien chi cham lop minh va chi khi chua cong bo (BR-08); sinh vien chi thay ket qua
 * cua minh SAU khi cong bo; chi Admin duoc mo khoa.
 */
class GradePolicy
{
    public function manage(User $user, ClassSection $section): bool
    {
        return $user->can(Permission::GRADES_MANAGE)
            && $section->isTaughtBy($user)
            && $section->status !== ClassSectionStatus::GradesPublished;
    }

    public function publish(User $user, ClassSection $section): bool
    {
        return $user->can(Permission::GRADES_PUBLISH) && $section->isTaughtBy($user);
    }

    public function unlock(User $user, ClassSection $section): bool
    {
        return $user->can(Permission::GRADES_UNLOCK);
    }

    public function view(User $user, FinalResult $result): bool
    {
        if ($user->can(Permission::GRADES_UNLOCK)) {
            return true;
        }

        if ($user->isLecturer()) {
            return $result->enrollment->classSection->isTaughtBy($user);
        }

        $studentId = $user->student?->id;

        return $studentId !== null
            && $user->can(Permission::GRADES_VIEW_OWN)
            && $result->published_at !== null
            && $studentId === $result->enrollment->student_id;
    }
}
