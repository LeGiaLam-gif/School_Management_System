<?php

namespace App\Modules\Enrollment\Policies;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\User;
use App\Shared\Authorization\Permission;

/**
 * Sinh vien chi thay/huy dang ky cua minh (chan IDOR, NFR-SEC-02); giang vien chi thay lop minh day.
 */
class EnrollmentPolicy
{
    public function create(User $user): bool
    {
        return $user->can(Permission::ENROLLMENTS_REGISTER);
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->can(Permission::ENROLLMENTS_OVERRIDE)) {
            return true;
        }

        if ($user->isLecturer()) {
            return $enrollment->classSection->isTaughtBy($user);
        }

        return $this->owns($user, $enrollment);
    }

    public function drop(User $user, Enrollment $enrollment): bool
    {
        return $user->can(Permission::ENROLLMENTS_OVERRIDE)
            || ($user->can(Permission::ENROLLMENTS_REGISTER) && $this->owns($user, $enrollment));
    }

    private function owns(User $user, Enrollment $enrollment): bool
    {
        $studentId = $user->student?->id;

        return $studentId !== null && $studentId === $enrollment->student_id;
    }
}
