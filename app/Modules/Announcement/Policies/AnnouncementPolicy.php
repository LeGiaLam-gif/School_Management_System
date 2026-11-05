<?php

namespace App\Modules\Announcement\Policies;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Models\User;
use App\Shared\Authorization\Permission;
use App\Shared\Enums\AnnouncementScope;

/**
 * Admin dang toan truong hoac moi lop; Lecturer chi dang cho lop minh (muc 6.8).
 */
class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ANNOUNCEMENTS_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ANNOUNCEMENTS_MANAGE);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if (! $user->can(Permission::ANNOUNCEMENTS_MANAGE)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $announcement->scope === AnnouncementScope::ClassSection
            && $announcement->classSection !== null
            && $announcement->classSection->isTaughtBy($user);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $this->update($user, $announcement);
    }
}
