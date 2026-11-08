<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('profile.show'))->assertRedirect(route('login'));
    }

    public function test_student_can_view_own_account_and_student_profile(): void
    {
        $user = User::factory()->student()->create([
            'name' => 'Nguyen Van Minh',
            'email' => 'student@example.com',
            'code' => 'SV00001',
        ]);
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'student_code' => 'SV00001',
            'cohort_year' => 2024,
            'phone' => '0901234567',
            'address' => 'Ha Noi',
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Nguyen Van Minh')
            ->assertSee('student@example.com')
            ->assertSee('SV00001')
            ->assertSee($student->program->name)
            ->assertSee($student->program->department->name)
            ->assertSee('2024')
            ->assertSee('0901234567')
            ->assertSee('Ha Noi')
            ->assertSee('Sinh viên');
    }

    public function test_lecturer_can_view_own_lecturer_profile(): void
    {
        $user = User::factory()->lecturer()->create();
        $lecturer = Lecturer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Thông tin giảng viên')
            ->assertSee($lecturer->lecturer_code)
            ->assertSee($lecturer->department->name);
    }

    public function test_admin_can_view_profile_without_student_or_lecturer_details(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('Quản trị viên')
            ->assertDontSee('Thông tin sinh viên')
            ->assertDontSee('Thông tin giảng viên');
    }
}
