<?php

namespace Tests\Feature\Identity;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Program;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\Gender;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\StudentStatus;
use App\Shared\Enums\UserStatus;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class AdminStudentManagementTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected User $admin;

    protected Program $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->seedRoles();

        $this->admin = User::factory()->withRole(RoleName::Admin)->create();
        $this->program = Program::factory()->create();
    }

    public function test_admin_can_view_student_index_page_and_stats(): void
    {
        Student::factory()->count(3)->create([
            'program_id' => $this->program->id,
            'status' => StudentStatus::Studying,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('Quản lý Sinh viên')
            ->assertSee('Tổng số sinh viên')
            ->assertSee('Đang theo học');

        $this->assertNotNull($response);
    }

    public function test_admin_can_filter_students_by_search_query(): void
    {
        $targetUser = User::factory()->student()->create([
            'name' => 'Nguyen Van An',
            'email' => 'an.nguyen@hta.edu.vn',
        ]);
        $targetStudent = Student::factory()->create([
            'user_id' => $targetUser->id,
            'student_code' => 'SV2026999',
            'program_id' => $this->program->id,
        ]);

        $otherStudent = Student::factory()->create([
            'student_code' => 'SV2026111',
            'program_id' => $this->program->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.index', ['search' => 'SV2026999']))
            ->assertOk()
            ->assertSee('SV2026999')
            ->assertDontSee('SV2026111');
    }

    public function test_admin_can_view_create_student_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.students.create'))
            ->assertOk()
            ->assertSee('Thêm Sinh Viên Mới')
            ->assertSee('Mã số sinh viên (MSSV)')
            ->assertSee('Ngành đào tạo');
    }

    public function test_admin_can_store_new_student_account_and_profile(): void
    {
        $adminClass = AdministrativeClass::create([
            'code' => 'CNTT2026A',
            'name' => 'Công nghệ thông tin 2026 A',
            'program_id' => $this->program->id,
            'academic_year' => 2026,
        ]);

        $payload = [
            'student_code' => 'SV2026001',
            'name' => 'Trần Quang Huy',
            'email' => 'huy.tq@student.hta.edu.vn',
            'password' => 'Password123!',
            'program_id' => $this->program->id,
            'class_id' => $adminClass->id,
            'cohort_year' => 2026,
            'date_of_birth' => '2004-05-15',
            'gender' => Gender::Male->value,
            'phone' => '0987654321',
            'address' => 'Hà Nội, Việt Nam',
            'status' => StudentStatus::Studying->value,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload);

        $student = Student::where('student_code', 'SV2026001')->first();
        $this->assertNotNull($student);

        $response->assertRedirect(route('admin.students.show', $student));

        $this->assertDatabaseHas('users', [
            'email' => 'huy.tq@student.hta.edu.vn',
            'code' => 'SV2026001',
            'name' => 'Trần Quang Huy',
        ]);

        $this->assertDatabaseHas('students', [
            'student_code' => 'SV2026001',
            'program_id' => $this->program->id,
            'class_id' => $adminClass->id,
            'cohort_year' => 2026,
        ]);

        $this->assertTrue($student->user->hasRole(RoleName::Student));
    }

    public function test_admin_can_view_student_details(): void
    {
        $student = Student::factory()->create([
            'program_id' => $this->program->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.students.show', $student))
            ->assertOk()
            ->assertSee($student->student_code)
            ->assertSee($student->user->name)
            ->assertSee('Lịch sử đăng ký học phần')
            ->assertSee('Cảnh báo học vụ');
    }

    public function test_admin_can_update_student_profile(): void
    {
        $student = Student::factory()->create([
            'program_id' => $this->program->id,
            'student_code' => 'SV2026002',
        ]);

        $updatePayload = [
            'student_code' => 'SV2026002',
            'name' => 'Trần Quang Huy (Cập nhật)',
            'email' => 'huy.updated@student.hta.edu.vn',
            'program_id' => $this->program->id,
            'cohort_year' => 2026,
            'status' => StudentStatus::Suspended->value,
            'user_status' => 'locked',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.students.update', $student), $updatePayload);

        $response->assertRedirect(route('admin.students.show', $student));

        $student->refresh();
        $this->assertSame('Trần Quang Huy (Cập nhật)', $student->user->name);
        $this->assertSame('huy.updated@student.hta.edu.vn', $student->user->email);
        $this->assertSame(StudentStatus::Suspended, $student->status);
        $this->assertSame(UserStatus::Locked, $student->user->status);
    }

    public function test_admin_can_toggle_student_account_status(): void
    {
        $student = Student::factory()->create([
            'program_id' => $this->program->id,
        ]);

        $this->assertTrue($student->user->isActive());

        // Toggle to lock
        $this->actingAs($this->admin)
            ->post(route('admin.students.toggle-status', $student))
            ->assertSessionHas('success');

        $student->refresh();
        $this->assertFalse($student->user->isActive());

        // Toggle to activate
        $this->actingAs($this->admin)
            ->post(route('admin.students.toggle-status', $student))
            ->assertSessionHas('success');

        $student->refresh();
        $this->assertTrue($student->user->isActive());
    }

    public function test_admin_can_delete_student_without_enrollments(): void
    {
        $student = Student::factory()->create([
            'program_id' => $this->program->id,
        ]);
        $userId = $student->user_id;

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.students.destroy', $student));

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    public function test_non_admin_cannot_access_student_management(): void
    {
        $studentUser = User::factory()->student()->create();

        $this->actingAs($studentUser)
            ->get(route('admin.students.index'))
            ->assertForbidden();

        $this->actingAs($studentUser)
            ->get(route('admin.students.create'))
            ->assertForbidden();
    }
}
