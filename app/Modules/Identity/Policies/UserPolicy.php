<?php

namespace App\Modules\Identity\Policies;

use App\Modules\Identity\Models\User;
use App\Shared\Authorization\Permission;

/**
 * Lop kiem tra 2 (theo doi tuong). Chi Super Admin (roles.manage) duoc
 * tao/sua/xoa Admin khac (muc 2.2).
 */
class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can(Permission::USERS_VIEW);
    }

    public function view(User $actor, User $target): bool
    {
        return $actor->is($target) || $actor->can(Permission::USERS_VIEW);
    }

    public function create(User $actor): bool
    {
        return $actor->can(Permission::USERS_CREATE);
    }

    public function update(User $actor, User $target): bool
    {
        return $actor->can(Permission::USERS_UPDATE) && $this->mayManage($actor, $target);
    }

    public function delete(User $actor, User $target): bool
    {
        return ! $actor->is($target)
            && $actor->can(Permission::USERS_DELETE)
            && $this->mayManage($actor, $target);
    }

    private function mayManage(User $actor, User $target): bool
    {
        return ! $target->isAdmin() || $actor->can(Permission::ROLES_MANAGE);
    }
}
