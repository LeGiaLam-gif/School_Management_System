<?php

namespace App\Modules\Attendance\Policies;

use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Authorization\Permission;

class AttendancePolicy
{
    /**
     * Giang vien chi diem danh buoi cua lop minh phu trach.
     */
    public function mark(User $user, ClassSession $session): bool
    {
        return $user->can(Permission::ATTENDANCE_MARK)
            && $session->classSection->isTaughtBy($user);
    }

    /**
     * Sinh vien chi xem diem danh cua chinh minh (IDOR); Admin xem tat ca.
     */
    public function view(User $user, AttendanceRecord $record): bool
    {
        if ($user->can(Permission::ATTENDANCE_VIEW_ALL)) {
            return true;
        }

        if ($user->isLecturer()) {
            return $record->enrollment->classSection->isTaughtBy($user);
        }

        $studentId = $user->student?->id;

        return $studentId !== null
            && $user->can(Permission::ATTENDANCE_VIEW_OWN)
            && $studentId === $record->enrollment->student_id;
    }
}
