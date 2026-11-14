<?php

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Support\AbsenceRateCalculator;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\SessionStatus;
use Throwable;

class StudentAttendanceService
{
    public function __construct(
        private readonly ?AbsenceRateCalculator $calculator = null
    ) {}

    /**
     * Lay bao cao diem danh va ty le vang cua sinh vien trong hoc ky.
     */
    public function getAttendanceReport(?User $user): object
    {
        // 1. Thu lay du lieu that tu DB neu co
        if ($user !== null) {
            try {
                $student = $user->student;
                if ($student) {
                    $enrollments = Enrollment::with([
                        'classSection.course',
                        'classSection.lecturer.user',
                        'classSection.sessions.room',
                        'attendanceRecords.classSession',
                    ])
                        ->where('student_id', $student->id)
                        ->where('status', EnrollmentStatus::Registered)
                        ->get();

                    if ($enrollments->isNotEmpty()) {
                        return $this->formatFromDb($user, $enrollments);
                    }
                }
            } catch (Throwable) {
                // DB chua co tables/chua setup -> dung Mock
            }
        }

        // 2. Mock Data Fallback
        return $this->getMockAttendanceReport();
    }

    private function getMockAttendanceReport(): object
    {
        $enrolledIds = session('mock_enrolled_section_ids', ['CS102.O11', 'SE201.O11']);

        $coursesMaster = [
            'CS102.O11' => (object) [
                'class_section_code' => 'CS102.O11',
                'course_code' => 'CS102',
                'course_name' => 'Kỹ thuật lập trình',
                'credits' => 4,
                'lecturer_name' => 'ThS. Hoàng Tuấn Anh',
                'held_sessions' => 8,
                'total_sessions' => 15,
                'present_count' => 8,
                'late_count' => 0,
                'excused_count' => 0,
                'absent_count' => 0,
                'absence_rate' => 0.0,
                'is_warned' => false,
                'is_banned' => false,
                'status_label' => 'Đủ điều kiện dự thi',
                'status_badge' => 'bg-success',
                'sessions' => [
                    (object) ['session_no' => 1, 'date' => '02/11/2026', 'time' => '07:00 - 09:40', 'room' => 'A1.02', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                    (object) ['session_no' => 2, 'date' => '04/11/2026', 'time' => '13:00 - 15:40', 'room' => 'Lab 2', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                    (object) ['session_no' => 3, 'date' => '09/11/2026', 'time' => '07:00 - 09:40', 'room' => 'A1.02', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                    (object) ['session_no' => 4, 'date' => '11/11/2026', 'time' => '13:00 - 15:40', 'room' => 'Lab 2', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                ],
            ],
            'SE201.O11' => (object) [
                'class_section_code' => 'SE201.O11',
                'course_code' => 'SE201',
                'course_name' => 'Nhập môn Công nghệ phần mềm',
                'credits' => 3,
                'lecturer_name' => 'TS. Trần Văn Hùng',
                'held_sessions' => 8,
                'total_sessions' => 15,
                'present_count' => 7,
                'late_count' => 0,
                'excused_count' => 0,
                'absent_count' => 1,
                'absence_rate' => 12.5,
                'is_warned' => true,
                'is_banned' => false,
                'status_label' => 'Đủ điều kiện (Cần lưu ý)',
                'status_badge' => 'bg-warning text-dark',
                'sessions' => [
                    (object) ['session_no' => 1, 'date' => '04/11/2026', 'time' => '07:00 - 09:40', 'room' => 'C1.01', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                    (object) ['session_no' => 2, 'date' => '06/11/2026', 'time' => '07:00 - 09:40', 'room' => 'C1.01', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                    (object) ['session_no' => 3, 'date' => '11/11/2026', 'time' => '07:00 - 09:40', 'room' => 'C1.01', 'status' => 'Vắng', 'badge' => 'bg-danger', 'note' => 'Vắng không phép'],
                    (object) ['session_no' => 4, 'date' => '13/11/2026', 'time' => '07:00 - 09:40', 'room' => 'C1.01', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                ],
            ],
            'CS201.O11' => (object) [
                'class_section_code' => 'CS201.O11',
                'course_code' => 'CS201',
                'course_name' => 'Cấu trúc dữ liệu và giải thuật',
                'credits' => 4,
                'lecturer_name' => 'TS. Nguyễn Minh Châu',
                'held_sessions' => 8,
                'total_sessions' => 15,
                'present_count' => 7,
                'late_count' => 1,
                'excused_count' => 0,
                'absent_count' => 0,
                'absence_rate' => 0.0,
                'is_warned' => false,
                'is_banned' => false,
                'status_label' => 'Đủ điều kiện dự thi',
                'status_badge' => 'bg-success',
                'sessions' => [
                    (object) ['session_no' => 1, 'date' => '02/11/2026', 'time' => '09:50 - 12:30', 'room' => 'B2.01', 'status' => 'Có mặt', 'badge' => 'bg-success', 'note' => 'Đúng giờ'],
                    (object) ['session_no' => 2, 'date' => '06/11/2026', 'time' => '07:00 - 09:40', 'room' => 'Lab 3', 'status' => 'Muộn', 'badge' => 'bg-warning text-dark', 'note' => 'Vào muộn 15p'],
                ],
            ],
        ];

        $enrolledCourses = collect();
        foreach ($enrolledIds as $id) {
            if (isset($coursesMaster[$id])) {
                $enrolledCourses->push($coursesMaster[$id]);
            }
        }

        if ($enrolledCourses->isEmpty()) {
            $enrolledCourses = collect([$coursesMaster['CS102.O11'], $coursesMaster['SE201.O11']]);
        }

        $totalHeld = $enrolledCourses->sum('held_sessions');
        $totalPresent = $enrolledCourses->sum('present_count');
        $totalAbsent = $enrolledCourses->sum('absent_count');
        $totalLate = $enrolledCourses->sum('late_count');
        $totalExcused = $enrolledCourses->sum('excused_count');
        $overallRate = $totalHeld > 0 ? round(($totalAbsent / $totalHeld) * 100, 1) : 0.0;

        return (object) [
            'semester_name' => 'Học kỳ 1 năm học 2026 - 2027',
            'overall_held' => $totalHeld,
            'overall_present' => $totalPresent,
            'overall_absent' => $totalAbsent,
            'overall_late' => $totalLate,
            'overall_excused' => $totalExcused,
            'overall_rate' => $overallRate,
            'absence_threshold' => 20.0,
            'courses' => $enrolledCourses,
        ];
    }

    private function formatFromDb(User $user, Collection $enrollments): object
    {
        $courses = collect();
        $totalHeld = 0;
        $totalAbsent = 0;
        $totalPresent = 0;
        $totalLate = 0;
        $totalExcused = 0;

        foreach ($enrollments as $enr) {
            $sec = $enr->classSection;
            $records = $enr->attendanceRecords;
            $heldSessions = $sec->sessions->where('status', SessionStatus::Held->value)->count();

            $absent = $records->where('status', AttendanceStatus::Absent)->count();
            $late = $records->where('status', AttendanceStatus::Late)->count();
            $excused = $records->where('status', AttendanceStatus::Excused)->count();
            $present = $records->where('status', AttendanceStatus::Present)->count();

            $rate = $heldSessions > 0 ? round(($absent / $heldSessions) * 100, 1) : 0.0;
            $isBanned = $rate >= 20.0;
            $isWarned = ($rate >= 10.0 && $rate < 20.0);

            $sessionList = $records->map(fn ($r) => (object) [
                'session_no' => $r->classSession->session_no ?? 1,
                'date' => $r->classSession->session_date ? $r->classSession->session_date->format('d/m/Y') : 'N/A',
                'time' => "Tiết {$r->classSession->start_period} - {$r->classSession->end_period}",
                'room' => $r->classSession->room?->code ?? 'Phòng học',
                'status' => $r->status->label(),
                'badge' => match ($r->status) {
                    AttendanceStatus::Present => 'bg-success',
                    AttendanceStatus::Late => 'bg-warning text-dark',
                    AttendanceStatus::Excused => 'bg-info text-dark',
                    AttendanceStatus::Absent => 'bg-danger',
                    default => 'bg-secondary',
                },
                'note' => $r->note ?? 'Đã ghi nhận',
            ])->all();

            $courses->push((object) [
                'class_section_code' => $sec->code,
                'course_code' => $sec->course->code,
                'course_name' => $sec->course->name,
                'credits' => $sec->course->credits,
                'lecturer_name' => $sec->lecturer?->user?->name ?? 'Chưa phân công',
                'held_sessions' => $heldSessions,
                'total_sessions' => 15,
                'present_count' => $present,
                'late_count' => $late,
                'excused_count' => $excused,
                'absent_count' => $absent,
                'absence_rate' => $rate,
                'is_warned' => $isWarned,
                'is_banned' => $isBanned,
                'status_label' => $isBanned ? 'Cấm thi (Vắng >= 20%)' : ($isWarned ? 'Đủ điều kiện (Cần lưu ý)' : 'Đủ điều kiện dự thi'),
                'status_badge' => $isBanned ? 'bg-danger' : ($isWarned ? 'bg-warning text-dark' : 'bg-success'),
                'sessions' => $sessionList,
            ]);

            $totalHeld += $heldSessions;
            $totalAbsent += $absent;
            $totalPresent += $present;
            $totalLate += $late;
            $totalExcused += $excused;
        }

        $overallRate = $totalHeld > 0 ? round(($totalAbsent / $totalHeld) * 100, 1) : 0.0;

        return (object) [
            'semester_name' => 'Học kỳ 1 năm học 2026 - 2027',
            'overall_held' => $totalHeld,
            'overall_present' => $totalPresent,
            'overall_absent' => $totalAbsent,
            'overall_late' => $totalLate,
            'overall_excused' => $totalExcused,
            'overall_rate' => $overallRate,
            'absence_threshold' => 20.0,
            'courses' => $courses,
        ];
    }
}
