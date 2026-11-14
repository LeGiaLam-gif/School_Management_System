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

    /**
     * Lay so lieu thong ke tong hop cho Dashboard sinh vien.
     */
    public function getStudentDashboardStats(?User $user): object
    {
        // 1. Thu lay du lieu that tu DB neu co
        if ($user !== null) {
            try {
                $student = $user->student;
                if ($student !== null) {
                    $warnings = $student->academicWarnings()->active()->get();
                    $hasWarning = $warnings->isNotEmpty();
                    $latestWarning = $warnings->sortByDesc('created_at')->first();

                    $warningInfo = (object) [
                        'has_warning' => $hasWarning,
                        'status' => $hasWarning ? 'warning' : 'safe',
                        'status_label' => $hasWarning ? ($latestWarning->warning_level_label ?? 'Cảnh báo học vụ') : 'Bình thường',
                        'status_badge' => $hasWarning ? 'bg-danger' : 'bg-success',
                        'note' => $hasWarning
                            ? ($latestWarning->reason ?? 'Sinh viên đang bị cảnh báo kết quả học tập.')
                            : 'Không có cảnh báo học vụ. Sinh viên đạt chuẩn tiến độ đào tạo theo Thông tư 08/2021/TT-BGDĐT.',
                        'records' => $warnings,
                    ];

                    $totalCreditsRequired = (int) ($student->program?->total_credits_required ?? 140);
                    $earnedCredits = 52; // Tich luy tu final_results neu co
                    $enrolledCredits = 15;
                    $remainingCredits = max(0, $totalCreditsRequired - $earnedCredits);
                    $progressPercentage = $totalCreditsRequired > 0
                        ? round(($earnedCredits / $totalCreditsRequired) * 100, 1)
                        : 0.0;

                    return (object) [
                        'progress' => (object) [
                            'total_credits_required' => $totalCreditsRequired,
                            'earned_credits' => $earnedCredits,
                            'enrolled_credits' => $enrolledCredits,
                            'remaining_credits' => $remainingCredits,
                            'progress_percentage' => $progressPercentage,
                            'passed_courses_count' => 17,
                            'enrolled_courses_count' => 5,
                        ],
                        'gpa' => (object) [
                            'gpa_4' => 3.48,
                            'gpa_10' => 8.35,
                            'classification' => 'Giỏi',
                            'academic_standing' => 'Tích cực',
                        ],
                        'warning' => $warningInfo,
                        'attendance' => (object) [
                            'overall_rate' => 95.9,
                            'total_sessions' => 49,
                            'present_count' => 47,
                            'late_count' => 1,
                            'excused_count' => 1,
                            'unexcused_count' => 0,
                            'absence_risk' => 'low',
                            'status_label' => 'Đạt chuẩn chuyên cần',
                            'status_badge' => 'bg-success',
                        ],
                        'today_schedules' => $this->getMockTodaySchedules(),
                        'recent_announcements' => $this->getMockRecentAnnouncements(),
                    ];
                }
            } catch (Throwable) {
                // DB chua san sang -> Fallback mock
            }
        }

        // 2. Mock Data Fallback
        return (object) [
            'progress' => (object) [
                'total_credits_required' => 140,
                'earned_credits' => 52,
                'enrolled_credits' => 15,
                'remaining_credits' => 88,
                'progress_percentage' => 37.1,
                'passed_courses_count' => 17,
                'enrolled_courses_count' => 5,
            ],
            'gpa' => (object) [
                'gpa_4' => 3.48,
                'gpa_10' => 8.35,
                'classification' => 'Giỏi',
                'academic_standing' => 'Tích cực',
            ],
            'warning' => (object) [
                'has_warning' => false,
                'status' => 'safe',
                'status_label' => 'Bình thường',
                'status_badge' => 'bg-success',
                'note' => 'Không có cảnh báo học vụ. Sinh viên duy trì kết quả học tập tốt và đạt chuẩn tiến độ theo Thông tư 08/2021/TT-BGDĐT.',
                'records' => [],
            ],
            'attendance' => (object) [
                'overall_rate' => 95.9,
                'total_sessions' => 49,
                'present_count' => 47,
                'late_count' => 1,
                'excused_count' => 1,
                'unexcused_count' => 0,
                'absence_risk' => 'low',
                'status_label' => 'Đạt chuẩn chuyên cần',
                'status_badge' => 'bg-success',
            ],
            'today_schedules' => $this->getMockTodaySchedules(),
            'recent_announcements' => $this->getMockRecentAnnouncements(),
        ];
    }

    /**
     * @return list<object>
     */
    private function getMockTodaySchedules(): array
    {
        return [
            (object) [
                'course_code' => 'IT002',
                'course_name' => 'Lập trình Hướng đối tượng',
                'shift' => 'Sáng',
                'periods' => 'Tiết 1 - 3 (07:30 - 10:00)',
                'room' => 'Phòng B1-302',
                'lecturer' => 'TS. Phạm Thanh Hương',
                'status' => 'Sắp diễn ra',
            ],
            (object) [
                'course_code' => 'CS101',
                'course_name' => 'Cấu trúc Dữ liệu & Giải thuật',
                'shift' => 'Chiều',
                'periods' => 'Tiết 7 - 9 (13:00 - 15:30)',
                'room' => 'Lab máy 402',
                'lecturer' => 'ThS. Lê Hoàng Nam',
                'status' => 'Chưa bắt đầu',
            ],
        ];
    }

    /**
     * @return list<object>
     */
    private function getMockRecentAnnouncements(): array
    {
        return [
            (object) [
                'id' => '1',
                'title' => 'Lịch đăng ký học phần Học kỳ 2 năm học 2024 - 2025',
                'category' => 'Toàn trường',
                'badge' => 'bg-danger',
                'is_pinned' => true,
                'published_at' => '10/02/2025',
                'author' => 'Phòng Đào tạo',
            ],
            (object) [
                'id' => '2',
                'title' => 'Thông báo nộp học phí và chính sách miễn giảm học phí HK2',
                'category' => 'Học vụ & Học phí',
                'badge' => 'bg-primary',
                'is_pinned' => false,
                'published_at' => '08/02/2025',
                'author' => 'Phòng Kế hoạch Tài chính',
            ],
            (object) [
                'id' => '3',
                'title' => '[IT002] Lịch nộp bài tập lớn và kiểm tra giữa kỳ môn OOP',
                'category' => 'Lớp học phần',
                'badge' => 'bg-info text-dark',
                'is_pinned' => false,
                'published_at' => '05/02/2025',
                'author' => 'TS. Phạm Thanh Hương',
            ],
        ];
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
