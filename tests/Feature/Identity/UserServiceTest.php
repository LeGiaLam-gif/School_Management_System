<?php

namespace Tests\Feature\Identity;

use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Identity\Events\UserCreated;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Services\UserService;
use App\Shared\Enums\UserStatus;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\CreatesRoles;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_create_student_creates_user_profile_and_role_together(): void
    {
        Event::fake([UserCreated::class]);
        $program = Program::factory()->create();

        $user = app(UserService::class)->createStudent(
            ['code' => 'SV00001', 'name' => 'Nguyễn Văn A', 'email' => 'sv1@example.com', 'password' => 'secret-pass'],
            ['student_code' => 'SV00001', 'program_id' => $program->id, 'cohort_year' => 2024],
        );

        $this->assertTrue($user->isStudent());
        $this->assertTrue($user->must_change_password);
        $this->assertSame('SV00001', $user->student->student_code);
        Event::assertDispatched(UserCreated::class);
    }

    public function test_failure_rolls_back_the_user_when_profile_is_invalid(): void
    {
        try {
            app(UserService::class)->createStudent(
                ['code' => 'SV00002', 'name' => 'B', 'email' => 'sv2@example.com', 'password' => 'secret-pass'],
                ['student_code' => 'SV00002', 'program_id' => 99999, 'cohort_year' => 2024], // program khong ton tai
            );
            $this->fail('Phải lỗi khóa ngoại.');
        } catch (QueryException) {
            // mong doi
        }

        $this->assertDatabaseMissing('users', ['email' => 'sv2@example.com']);
        $this->assertSame(0, Student::count());
    }

    public function test_create_lecturer(): void
    {
        $department = Department::factory()->create();

        $user = app(UserService::class)->createLecturer(
            ['code' => 'GV001', 'name' => 'GV', 'email' => 'gv1@example.com', 'password' => 'secret-pass'],
            ['lecturer_code' => 'GV001', 'department_id' => $department->id],
        );

        $this->assertTrue($user->isLecturer());
        $this->assertSame($department->id, $user->lecturer->department_id);
    }

    public function test_deactivate_and_activate(): void
    {
        $user = User::factory()->student()->create();
        $service = app(UserService::class);

        $this->assertSame(UserStatus::Locked, $service->deactivate($user)->status);
        $this->assertSame(UserStatus::Active, $service->activate($user)->status);
    }

    public function test_user_is_soft_deleted_not_removed(): void
    {
        $user = User::factory()->student()->create();

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}
