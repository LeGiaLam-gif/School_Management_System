<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        $lecturer = Lecturer::factory()->create([
            'user_id' => $user->id,
            'phone' => '0562845621',
            'address' => '551/156 Lê Văn Khương, phường Tân Thới Hiệp, TP.HCM',
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('sms-profile-summary', false)
            ->assertSee('Thông tin liên hệ')
            ->assertSee('Thông tin giảng viên')
            ->assertSee($lecturer->lecturer_code)
            ->assertSee($lecturer->department->name)
            ->assertSee('0562845621')
            ->assertSee('551/156 Lê Văn Khương, phường Tân Thới Hiệp, TP.HCM')
            ->assertSee('Lưu thông tin liên hệ');
    }

    public function test_admin_can_view_profile_without_student_or_lecturer_details(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('sms-profile-summary', false)
            ->assertSee('Quản trị viên')
            ->assertSee('Thông tin liên hệ')
            ->assertSee('Lưu thông tin liên hệ')
            ->assertSee('Đổi mật khẩu tài khoản')
            ->assertDontSee('Thông tin sinh viên')
            ->assertDontSee('Thông tin giảng viên');
    }

    public function test_lecturer_can_update_own_contact_information(): void
    {
        $lecturer = Lecturer::factory()->create([
            'phone' => '0900000000',
            'address' => 'Địa chỉ cũ',
        ]);

        $this->actingAs($lecturer->user)
            ->put(route('profile.contact'), [
                'phone' => '0562845621',
                'address' => '551/156 Lê Văn Khương, phường Tân Thới Hiệp, TP.HCM',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $lecturer->refresh();
        $this->assertSame('0562845621', $lecturer->phone);
        $this->assertSame('551/156 Lê Văn Khương, phường Tân Thới Hiệp, TP.HCM', $lecturer->address);
    }

    public function test_admin_can_update_own_contact_information(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('profile.contact'), [
                'phone' => '0901234567',
                'address' => 'Địa chỉ liên hệ Admin',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $admin->refresh();
        $this->assertSame('0901234567', $admin->phone);
        $this->assertSame('Địa chỉ liên hệ Admin', $admin->address);
    }

    public function test_admin_lecturer_and_student_can_change_their_own_password(): void
    {
        foreach ([RoleName::Admin, RoleName::Lecturer, RoleName::Student] as $role) {
            $user = User::factory()->withRole($role)->create([
                'password' => 'old-password',
                'must_change_password' => true,
            ]);

            $this->actingAs($user)
                ->put(route('profile.password'), [
                    'current_password' => 'old-password',
                    'new_password' => 'new-secure-password',
                    'new_password_confirmation' => 'new-secure-password',
                ])
                ->assertSessionHasNoErrors()
                ->assertSessionHas('success');

            $user->refresh();
            $this->assertTrue(Hash::check('new-secure-password', $user->password));
            $this->assertFalse($user->must_change_password);
        }
    }

    public function test_shared_password_form_is_available_on_each_role_profile(): void
    {
        foreach ([RoleName::Admin, RoleName::Lecturer, RoleName::Student] as $role) {
            $user = User::factory()->withRole($role)->create();

            $this->actingAs($user)
                ->get(route('profile.show'))
                ->assertOk()
                ->assertSee(route('profile.password'))
                ->assertSee('Đổi mật khẩu');
        }
    }
}
