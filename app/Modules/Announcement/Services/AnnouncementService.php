<?php

namespace App\Modules\Announcement\Services;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\AnnouncementScope;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\UserStatus;
use Illuminate\Support\Collection;

/**
 * Con thieu (Sprint 5): publish (hen gio, ghim), Job SendAnnouncementNotification (bang notifications cua Laravel).
 */
class AnnouncementService
{
    /**
     * Xac dinh nguoi nhan (muc 6.8): `all` -> moi nguoi dung dang hoat dong;
     * `class_section` -> chi sinh vien dang co dang ky `registered` trong lop do.
     *
     * @return Collection<int, User>
     */
    public function resolveAudience(Announcement $announcement): Collection
    {
        if ($announcement->scope === AnnouncementScope::All) {
            return User::query()->where('status', UserStatus::Active->value)->get();
        }

        return User::query()
            ->where('status', UserStatus::Active->value)
            ->whereHas('student.enrollments', function ($q) use ($announcement) {
                $q->where('class_section_id', $announcement->class_section_id)
                    ->where('status', EnrollmentStatus::Registered->value);
            })
            ->get();
    }
}
