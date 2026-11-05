<?php

namespace Tests\Unit\Shared;

use App\Shared\Authorization\Permission;
use PHPUnit\Framework\TestCase;

class PermissionCatalogTest extends TestCase
{
    public function test_every_role_permission_exists_in_the_catalog(): void
    {
        $all = Permission::all();

        foreach (Permission::byRole() as $role => $permissions) {
            foreach ($permissions as $permission) {
                $this->assertContains($permission, $all, "Role {$role} dùng permission không có trong danh mục: {$permission}");
            }
        }
    }

    public function test_permissions_follow_resource_action_naming(): void
    {
        foreach (Permission::all() as $permission) {
            $this->assertMatchesRegularExpression('/^[a-z_]+\.[a-z_]+$/', $permission);
        }
    }

    public function test_roles_manage_is_not_granted_to_any_role(): void
    {
        // Chi Super Admin (gan truc tiep) duoc tao/sua Admin khac (muc 2.2).
        foreach (Permission::byRole() as $permissions) {
            $this->assertNotContains(Permission::ROLES_MANAGE, $permissions);
        }
    }

    public function test_students_cannot_manage_anything(): void
    {
        $student = Permission::byRole()['student'];

        foreach ($student as $permission) {
            $this->assertStringNotContainsString('manage', $permission);
            $this->assertStringNotContainsString('unlock', $permission);
            $this->assertStringNotContainsString('override', $permission);
        }
    }

    public function test_only_admin_can_unlock_grades_and_view_audit(): void
    {
        $roles = Permission::byRole();

        $this->assertContains(Permission::GRADES_UNLOCK, $roles['admin']);
        $this->assertContains(Permission::AUDIT_VIEW, $roles['admin']);
        $this->assertNotContains(Permission::GRADES_UNLOCK, $roles['lecturer']);
        $this->assertNotContains(Permission::AUDIT_VIEW, $roles['lecturer']);
    }
}
