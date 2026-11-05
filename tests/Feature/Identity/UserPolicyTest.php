<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\User;
use App\Shared\Authorization\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_admin_can_manage_students_but_not_other_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();

        $this->assertTrue($admin->can('update', $student));
        $this->assertFalse($admin->can('update', $otherAdmin));
        $this->assertFalse($admin->can('delete', $otherAdmin));
    }

    public function test_super_admin_can_manage_other_admins(): void
    {
        $superAdmin = User::factory()->admin()->create();
        $superAdmin->givePermissionTo(Permission::ROLES_MANAGE);
        $otherAdmin = User::factory()->admin()->create();

        $this->assertTrue($superAdmin->can('update', $otherAdmin));
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_student_cannot_manage_users_but_can_view_self(): void
    {
        $student = User::factory()->student()->create();
        $other = User::factory()->student()->create();

        $this->assertFalse($student->can('viewAny', User::class));
        $this->assertFalse($student->can('create', User::class));
        $this->assertTrue($student->can('view', $student));
        $this->assertFalse($student->can('view', $other));
    }

    public function test_lecturer_cannot_manage_users(): void
    {
        $lecturer = User::factory()->lecturer()->create();

        $this->assertFalse($lecturer->can('create', User::class));
        $this->assertFalse($lecturer->can('update', User::factory()->student()->create()));
    }
}
