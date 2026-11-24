<?php

namespace App\Modules\Identity\Services;

use App\Modules\Academic\Models\Room;
use App\Modules\Announcement\Models\Announcement;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSchedule;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\AnnouncementScope;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\SessionStatus;
use App\Shared\Enums\StudentStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
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
                    ->with(['program.department', 'administrativeClass.advisorLecturer.user'])
                    ->first();

                if ($student !== null) {
                    $advisor = $student->administrativeClass?->advisorLecturer;

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
                        'department_name' => $student->program->department->name ?? 'Công nghệ Thông tin',
                        'department_code' => $student->program->department->code ?? 'CNTT',
                        'program_name' => $student->program->name ?? 'Kỹ thuật Phần mềm',
                        'program_code' => $student->program->code ?? 'SE01',
                        'class_code' => $student->administrativeClass->code ?? 'SE23.01',
                        'class_name' => $student->administrativeClass->name ?? 'Lớp Kỹ thuật Phần mềm 2023-01',
                        'advisor_name' => $advisor?->user?->name,
                        'advisor_academic_title' => $advisor?->academic_title,
                        'advisor_email' => $advisor?->user?->email,
                        'duration_years' => $student->program->duration_years ?? 4,
                        'total_credits_required' => $student->program->total_credits_required ?? 140,
                    ];
                }
            } catch (Throwable) {
                // Database chua san sang hoac khong the ket noi -> chuyen xuong dung Mock Data
            }
        }

        // 2. Mock Data mau day du
        return (object) [
            'user_id' => $user->id ?? 1,
            'student_code' => $user->code ?? 'SV20230001',
            'name' => $user->name ?? 'Nguyễn Văn An',
            'email' => $user->email ?? 'nguyenvanan.sv@sms.edu.vn',
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
            'advisor_academic_title' => null,
            'advisor_email' => null,
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

                    $totalCreditsRequired = (int) ($student->program->total_credits_required ?? 120);

                    // 1.1. Tinh tin chi tich luy va GPA that tu bang final_results (dong bo voi StudentGradeService)
                    $publishedResults = FinalResult::with(['enrollment.classSection.course'])
                        ->published()
                        ->whereHas('enrollment', fn ($q) => $q->where('student_id', $student->id))
                        ->get();

                    $activeEnrollments = Enrollment::with([
                        'classSection.course',
                        'classSection.lecturer.user',
                        'classSection.schedules.room',
                        'classSection.sessions',
                        'attendanceRecords',
                    ])
                        ->where('student_id', $student->id)
                        ->where('status', EnrollmentStatus::Registered)
                        ->get();

                    if ($publishedResults->isNotEmpty() || $activeEnrollments->isNotEmpty()) {
                        $earnedCredits = 0;
                        $passedCoursesCount = 0;
                        $totalWeightedGpa4 = 0.0;
                        $totalWeightedGpa10 = 0.0;
                        $totalGradedCredits = 0;

                        foreach ($publishedResults as $res) {
                            $enr = $res->enrollment;
                            if (! $enr instanceof Enrollment) {
                                continue;
                            }
                            $sec = $enr->classSection;
                            if (! $sec instanceof ClassSection) {
                                continue;
                            }
                            $course = $sec->course;
                            $credits = $course->credits;
                            $totalGradedCredits += $credits;
                            $totalWeightedGpa4 += ($res->gpa_point * $credits);
                            $totalWeightedGpa10 += ($res->total_score * $credits);

                            if ($res->is_passed) {
                                $earnedCredits += $credits;
                                $passedCoursesCount++;
                            }
                        }

                        $gpa4 = $totalGradedCredits > 0 ? round($totalWeightedGpa4 / $totalGradedCredits, 2) : 0.0;
                        $gpa10 = $totalGradedCredits > 0 ? round($totalWeightedGpa10 / $totalGradedCredits, 2) : 0.0;
                        $classification = match (true) {
                            $gpa4 >= 3.6 => 'Xuất sắc',
                            $gpa4 >= 3.2 => 'Giỏi',
                            $gpa4 >= 2.5 => 'Khá',
                            $gpa4 >= 2.0 => 'Trung bình',
                            default => 'Yếu',
                        };

                        $enrolledCredits = 0;
                        $totalHeldSessions = 0;
                        $presentCount = 0;
                        $lateCount = 0;
                        $excusedCount = 0;
                        $absentCount = 0;

                        foreach ($activeEnrollments as $enr) {
                            $sec = $enr->classSection;
                            if (! $sec instanceof ClassSection) {
                                continue;
                            }
                            $enrolledCredits += $sec->course->credits;
                            $totalHeldSessions += $sec->sessions->where('status', SessionStatus::Held)->count();

                            $records = $enr->attendanceRecords;
                            $presentCount += $records->where('status', AttendanceStatus::Present)->count();
                            $lateCount += $records->where('status', AttendanceStatus::Late)->count();
                            $excusedCount += $records->where('status', AttendanceStatus::Excused)->count();
                            $absentCount += $records->where('status', AttendanceStatus::Absent)->count();
                        }

                        $remainingCredits = max(0, $totalCreditsRequired - $earnedCredits);
                        $progressPercentage = $totalCreditsRequired > 0
                            ? round(($earnedCredits / $totalCreditsRequired) * 100, 1)
                            : 0.0;

                        // Ty le chuyen can = 100% - ty le vang (dong bo voi StudentAttendanceService)
                        $absenceRate = $totalHeldSessions > 0
                            ? round(($absentCount / $totalHeldSessions) * 100, 1)
                            : 0.0;
                        $attendanceRate = round(max(0.0, 100.0 - $absenceRate), 1);

                        return (object) [
                            'progress' => (object) [
                                'total_credits_required' => $totalCreditsRequired,
                                'earned_credits' => $earnedCredits,
                                'enrolled_credits' => $enrolledCredits,
                                'remaining_credits' => $remainingCredits,
                                'progress_percentage' => $progressPercentage,
                                'passed_courses_count' => $passedCoursesCount,
                                'enrolled_courses_count' => $activeEnrollments->count(),
                            ],
                            'gpa' => (object) [
                                'gpa_4' => $gpa4,
                                'gpa_10' => $gpa10,
                                'classification' => $classification,
                                'academic_standing' => $hasWarning ? 'Cảnh báo' : 'Tích cực',
                            ],
                            'warning' => $warningInfo,
                            'attendance' => (object) [
                                'overall_rate' => $attendanceRate,
                                'total_sessions' => $totalHeldSessions,
                                'present_count' => $presentCount,
                                'late_count' => $lateCount,
                                'excused_count' => $excusedCount,
                                'unexcused_count' => $absentCount,
                                'absence_risk' => $absenceRate >= 20.0 ? 'high' : ($absenceRate >= 10.0 ? 'medium' : 'low'),
                                'status_label' => $absenceRate >= 20.0 ? 'Cấm thi (Vắng >= 20%)' : 'Đạt chuẩn chuyên cần',
                                'status_badge' => $absenceRate >= 20.0 ? 'bg-danger' : 'bg-success',
                            ],
                            'today_schedules' => $this->buildDbSchedules($activeEnrollments),
                            'recent_announcements' => $this->buildDbRecentAnnouncements($student),
                        ];
                    }
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
     * Lay lich hoc TRONG NGAY HOM NAY tu cac lop hoc phan sinh vien dang dang ky (dong bo 100% voi cot Hom nay trong Thoi khoa bieu).
     *
     * @param  Collection<int, Enrollment>  $activeEnrollments
     * @return list<object>
     */
    private function buildDbSchedules(Collection $activeEnrollments): array
    {
        $todayIso = (int) now()->dayOfWeekIso;
        $todayItems = [];

        $startTimes = [
            1 => '07:00', 2 => '07:55', 3 => '08:50',
            4 => '09:50', 5 => '10:45', 6 => '11:40',
            7 => '13:00', 8 => '13:55', 9 => '14:50',
            10 => '15:50', 11 => '16:45', 12 => '17:40',
        ];

        $endTimes = [
            1 => '07:50', 2 => '08:45', 3 => '09:40',
            4 => '10:40', 5 => '11:35', 6 => '12:30',
            7 => '13:50', 8 => '14:45', 9 => '15:40',
            10 => '16:40', 11 => '17:35', 12 => '18:30',
        ];

        foreach ($activeEnrollments as $enr) {
            $sec = $enr->classSection;
            if (! $sec instanceof ClassSection) {
                continue;
            }

            $course = $sec->course;
            $lecturer = $sec->lecturer;
            $lecturerTitle = $lecturer instanceof Lecturer && $lecturer->academic_title ? ($lecturer->academic_title.'. ') : '';
            $lecturerName = $lecturer instanceof Lecturer ? ($lecturerTitle.$lecturer->user->name) : 'Chưa phân công';

            foreach ($sec->schedules as $sch) {
                if (! $sch instanceof ClassSchedule) {
                    continue;
                }

                $dayNum = (int) $sch->day_of_week;
                if ($dayNum !== $todayIso) {
                    continue;
                }

                $startP = (int) $sch->start_period;
                $endP = (int) $sch->end_period;
                $shift = $startP <= 6 ? 'Sáng' : 'Chiều';
                $timeRange = ($startTimes[$startP] ?? '07:00').' - '.($endTimes[$endP] ?? '11:35');
                $room = $sch->room;
                $roomCode = $room instanceof Room ? ('Phòng '.$room->code) : 'Phòng học';

                $todayItems[] = (object) [
                    'course_code' => $course->code,
                    'course_name' => $course->name,
                    'shift' => $shift,
                    'periods' => "Tiết {$startP} - {$endP} ({$timeRange})",
                    'room' => $roomCode,
                    'lecturer' => $lecturerName,
                    'status' => 'Hôm nay',
                    'sort_key' => $startP,
                ];
            }
        }

        usort($todayItems, fn (object $a, object $b): int => $a->sort_key <=> $b->sort_key);

        return $todayItems;
    }

    /**
     * Lay thong bao that tu bang announcements (Toan truong + Lop hoc phan sinh vien dang hoc).
     *
     * @return list<object>
     */
    private function buildDbRecentAnnouncements(Student $student): array
    {
        $enrolledSectionIds = $student->enrollments()
            ->where('status', EnrollmentStatus::Registered)
            ->pluck('class_section_id')
            ->all();

        $announcements = Announcement::published()
            ->with(['author', 'classSection'])
            ->where(function ($q) use ($enrolledSectionIds) {
                $q->where('scope', AnnouncementScope::All->value)
                    ->orWhere(function ($sub) use ($enrolledSectionIds) {
                        $sub->where('scope', AnnouncementScope::ClassSection->value)
                            ->whereIn('class_section_id', $enrolledSectionIds);
                    });
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->get();

        return $announcements->map(function (Announcement $a): object {
            $isAll = $a->scope === AnnouncementScope::All;
            $secCode = $a->classSection instanceof ClassSection ? $a->classSection->code : '';
            $pubDate = $a->published_at instanceof CarbonInterface
                ? $a->published_at->format('d/m/Y')
                : 'N/A';

            return (object) [
                'id' => (string) $a->id,
                'title' => $a->title,
                'category' => $isAll ? 'Toàn trường' : "Lớp {$secCode}",
                'badge' => $isAll ? 'bg-primary' : 'bg-info text-dark',
                'is_pinned' => $a->is_pinned,
                'published_at' => $pubDate,
                'author' => $a->author instanceof User ? $a->author->name : 'Phòng Đào tạo',
            ];
        })->values()->all();
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
