<?php

namespace App\Modules\Identity\Services;

use App\Modules\Identity\Events\UserCreated;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\UserStatus;
use Illuminate\Support\Facades\DB;

/**
 * Nghiep vu tai khoan (muc 6.2): tao users + ho so trong CUNG 1 transaction.
 */
class UserService
{
    /**
     * @param  array{code: string, name: string, email: string, password: string}  $account
     * @param  array<string, mixed>  $profile  cot cua bang students (khong gom user_id)
     */
    public function createStudent(array $account, array $profile): User
    {
        return $this->createWithProfile($account, RoleName::Student, function (User $user) use ($profile) {
            Student::create($profile + ['user_id' => $user->id]);
        });
    }

    /**
     * @param  array{code: string, name: string, email: string, password: string}  $account
     * @param  array<string, mixed>  $profile  cot cua bang lecturers (khong gom user_id)
     */
    public function createLecturer(array $account, array $profile): User
    {
        return $this->createWithProfile($account, RoleName::Lecturer, function (User $user) use ($profile) {
            Lecturer::create($profile + ['user_id' => $user->id]);
        });
    }

    /**
     * Khoa tai khoan: khong xoa cung nguoi dung da co du lieu hoc vu.
     */
    public function deactivate(User $user): User
    {
        $user->update(['status' => UserStatus::Locked]);

        return $user;
    }

    public function activate(User $user): User
    {
        $user->update(['status' => UserStatus::Active]);

        return $user;
    }

    /**
     * @param  array{code: string, name: string, email: string, password: string}  $account
     * @param  callable(User): void  $createProfile
     */
    private function createWithProfile(array $account, RoleName $role, callable $createProfile): User
    {
        $user = DB::transaction(function () use ($account, $role, $createProfile) {
            $user = User::create($account + [
                'status' => UserStatus::Active,
                // Mat khau do Admin cap: buoc doi o lan dang nhap dau (FR-AUTH-04).
                'must_change_password' => true,
            ]);

            $user->assignRole($role->value);
            $createProfile($user);

            return $user;
        });

        UserCreated::dispatch($user);

        return $user;
    }
}
