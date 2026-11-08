<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/student/profile')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_profile(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student/profile')->assertStatus(403);
    }

    public function test_admin_cannot_access_student_profile(): void
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $this->actingAs($admin)->get('/student/profile')->assertStatus(403);
    }

    public function test_student_can_view_profile_with_academic_info(): void
    {
        /** @var Student $student */
        $student = Student::factory()->create();
        $user = $student->user;

        $response = $this->actingAs($user)->get('/student/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
        $response->assertSee($student->student_code);
        $response->assertSee((string) $student->cohort_year);
        $response->assertSee($student->program->name);
        $response->assertSee($student->program->department->name);
    }

    public function test_student_can_update_contact_info(): void
    {
        /** @var Student $student */
        $student = Student::factory()->create([
            'phone' => '0900000001',
            'address' => 'Địa chỉ cũ',
        ]);

        $response = $this->actingAs($student->user)->put('/student/profile/contact', [
            'phone' => '0988776655',
            'address' => '123 Đường Mới, Quận 1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertSame('0988776655', $student->phone);
        $this->assertSame('123 Đường Mới, Quận 1', $student->address);
    }

    public function test_student_cannot_update_invalid_phone(): void
    {
        /** @var Student $student */
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->put('/student/profile/contact', [
            'phone' => 'phone-chua-chu-cai-abc',
            'address' => 'Địa chỉ',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    public function test_student_can_change_password(): void
    {
        $user = User::factory()->student()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->put('/student/profile/password', [
            'current_password' => 'old-password',
            'new_password' => 'new-secure-password',
            'new_password_confirmation' => 'new-secure-password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('new-secure-password', $user->password));
    }

    public function test_student_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->student()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->actingAs($user)->put('/student/profile/password', [
            'current_password' => 'wrong-password',
            'new_password' => 'new-secure-password',
            'new_password_confirmation' => 'new-secure-password',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }
}
