<?php

namespace App\Modules\Identity\Services;

use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\StudentStatus;
use Illuminate\Support\Facades\Hash;
use Throwable;

/**
 * Service cung cap du lieu sinh vien voi co che tu dong Fallback Mock Data.
 */
class StudentMockService
{
    /**
     * Lay ho so sinh vien (uu tien DB, fallback ve Mock Data).
     */
    public function getStudentProfile(?User $user): object
    {
        // 1. Thu lay tu Database neu da co ket noi va du lieu
        if ($user !== null) {
            try {
                /** @var Student|null $student */
                $student = $user->student()
                    ->with(['program.department', 'administrativeClass.advisor.user'])
                    ->first();

                if ($student !== null) {
                    return (object) [
                        'user_id' => $user->id,
                        'student_code' => $student->student_code ?? $user->code,
                        'name' => $user->name,
                        'email' => $user->email,
                        'status_label' => $student->status?->label() ?? 'Đang học',
                        'status_badge' => $this->getStatusBadgeClass($student->status),
                        'cohort_year' => $student->cohort_year ?? 2023,
                        'date_of_birth' => $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : '15/08/2005',
                        'gender' => $student->gender?->label() ?? 'Nam',
                        'phone' => session('mock_student_phone', $student->phone ?? '0912345678'),
                        'address' => session('mock_student_address', $student->address ?? '123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh'),
                        'department_name' => $student->program?->department?->name ?? 'Công nghệ Thông tin',
                        'department_code' => $student->program?->department?->code ?? 'CNTT',
                        'program_name' => $student->program?->name ?? 'Kỹ thuật Phần mềm',
                        'program_code' => $student->program?->code ?? 'SE01',
                        'class_code' => $student->administrativeClass?->code ?? 'SE23.01',
                        'class_name' => $student->administrativeClass?->name ?? 'Lớp Kỹ thuật Phần mềm 2023-01',
                        'advisor_name' => $student->administrativeClass?->advisor?->user?->name ?? 'ThS. Hoàng Tuấn Anh',
                        'duration_years' => $student->program?->duration_years ?? 4,
                        'total_credits_required' => $student->program?->total_credits_required ?? 140,
                    ];
                }
            } catch (Throwable) {
                // Database chua san sang hoac khong the ket noi -> chuyen xuong dung Mock Data
            }
        }

        // 2. Mock Data mau day du
        return (object) [
            'user_id' => $user?->id ?? 1,
            'student_code' => $user?->code ?? 'SV20230001',
            'name' => $user?->name ?? 'Nguyễn Văn An',
            'email' => $user?->email ?? 'nguyenvanan.sv@sms.edu.vn',
            'status_label' => 'Đang học',
            'status_badge' => 'bg-success',
            'cohort_year' => 2023,
            'date_of_birth' => '15/08/2005',
            'gender' => 'Nam',
            'phone' => session('mock_student_phone', '0912345678'),
            'address' => session('mock_student_address', '123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh'),
            'department_name' => 'Công nghệ Thông tin',
            'department_code' => 'CNTT',
            'program_name' => 'Kỹ thuật Phần mềm',
            'program_code' => 'SE01',
            'class_code' => 'SE23.01',
            'class_name' => 'Lớp Kỹ thuật Phần mềm 2023-01',
            'advisor_name' => 'ThS. Hoàng Tuấn Anh',
            'duration_years' => 4,
            'total_credits_required' => 140,
        ];
    }

    /**
     * Cap nhat thong tin lien lac.
     */
    public function updateContact(?User $user, ?string $phone, ?string $address): void
    {
        session([
            'mock_student_phone' => $phone,
            'mock_student_address' => $address,
        ]);

        if ($user !== null) {
            try {
                /** @var Student $student */
                $student = $user->student()->firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_code' => $user->code,
                        'status' => StudentStatus::Studying,
                    ]
                );

                $student->update(array_filter([
                    'phone' => $phone,
                    'address' => $address,
                ], fn ($val) => $val !== null));
            } catch (Throwable) {
                // Bo qua loi neu chua co DB
            }
        }
    }

    /**
     * Cap nhat mat khau.
     */
    public function updatePassword(?User $user, string $newPassword): void
    {
        session(['mock_student_password_changed' => true]);

        if ($user !== null) {
            try {
                $user->update([
                    'password' => Hash::make($newPassword),
                    'must_change_password' => false,
                ]);
            } catch (Throwable) {
                // Bo qua
            }
        }
    }

    private function getStatusBadgeClass(?StudentStatus $status): string
    {
        return match ($status) {
            StudentStatus::Studying => 'bg-success',
            StudentStatus::Suspended => 'bg-warning text-dark',
            StudentStatus::Graduated => 'bg-info text-dark',
            StudentStatus::DroppedOut => 'bg-danger',
            default => 'bg-success',
        };
    }
}
