<?php

namespace App\Modules\Teaching\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\EnrollmentStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class StudentTimetableService
{
    /**
     * Lay du lieu thoi khoa bieu cua sinh vien theo tuan va theo danh sach.
     */
    public function getTimetableData(?User $user, int $weekOffset = 0): object
    {
        // 1. Tinh toan thoi gian tuan dang xem
        $targetDate = now()->addWeeks($weekOffset);
        $startOfWeek = $targetDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $targetDate->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $dayDate = $startOfWeek->copy()->addDays($i);
            $dayNum = $i + 1; // 1 = Thu hai ... 7 = Chu nhat
            $days[$dayNum] = [
                'day_of_week' => $dayNum,
                'name' => match ($dayNum) {
                    1 => 'Thứ Hai',
                    2 => 'Thứ Ba',
                    3 => 'Thứ Tư',
                    4 => 'Thứ Năm',
                    5 => 'Thứ Sáu',
                    6 => 'Thứ Bảy',
                    7 => 'Chủ Nhật',
                    default => 'N/A',
                },
                'date_formatted' => $dayDate->format('d/m'),
                'full_date' => $dayDate->format('d/m/Y'),
                'is_today' => $dayDate->isToday(),
            ];
        }

        // 2. Lay danh sach lich hoc (Uu tien DB, Fallback sang Mock)
        $scheduleItems = $this->getStudentSchedules($user);

        // 3. To chuc du lieu theo luoi thoi khoa bieu (4 Ca hoc: Ca 1 (Tiet 1-3), Ca 2 (Tiet 4-6), Ca 3 (Tiet 7-9), Ca 4 (Tiet 10-12))
        $shifts = [
            1 => ['label' => 'Sáng (Tiết 1 - 3)', 'time' => '07:00 - 09:40', 'start' => 1, 'end' => 3],
            2 => ['label' => 'Sáng (Tiết 4 - 6)', 'time' => '09:50 - 12:30', 'start' => 4, 'end' => 6],
            3 => ['label' => 'Chiều (Tiết 7 - 9)', 'time' => '13:00 - 15:40', 'start' => 7, 'end' => 9],
            4 => ['label' => 'Chiều (Tiết 10 - 12)', 'time' => '15:50 - 18:30', 'start' => 10, 'end' => 12],
        ];

        $grid = [];
        foreach ($shifts as $shiftId => $shiftInfo) {
            $grid[$shiftId] = [
                'shift_info' => $shiftInfo,
                'days' => [],
            ];
            foreach ($days as $dayNum => $dayInfo) {
                // Tim cac lich hoc roi vao ca nay va thu nay
                $matches = $scheduleItems->filter(function ($item) use ($dayNum, $shiftInfo) {
                    return (int) $item->day_of_week === (int) $dayNum
                        && (int) $item->start_period <= (int) $shiftInfo['end']
                        && (int) $item->end_period >= (int) $shiftInfo['start'];
                })->values();

                $grid[$shiftId]['days'][$dayNum] = $matches;
            }
        }

        return (object) [
            'week_offset' => $weekOffset,
            'week_label' => 'Tuần từ ' . $startOfWeek->format('d/m/Y') . ' đến ' . $endOfWeek->format('d/m/Y'),
            'start_of_week' => $startOfWeek->format('d/m/Y'),
            'end_of_week' => $endOfWeek->format('d/m/Y'),
            'days' => $days,
            'shifts' => $shifts,
            'grid' => $grid,
            'all_schedules' => $scheduleItems,
            'total_courses' => $scheduleItems->pluck('class_section_code')->unique()->count(),
            'total_credits' => $scheduleItems->unique('class_section_code')->sum('credits'),
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function getStudentSchedules(?User $user): Collection
    {
        if ($user !== null) {
            try {
                $student = $user->student;
                if ($student) {
                    $enrollments = Enrollment::with([
                        'classSection.course',
                        'classSection.lecturer.user',
                        'classSection.schedules.room',
                    ])
                        ->where('student_id', $student->id)
                        ->where('status', EnrollmentStatus::Registered)
                        ->get();

                    if ($enrollments->isNotEmpty()) {
                        $items = collect();
                        foreach ($enrollments as $enr) {
                            $sec = $enr->classSection;
                            foreach ($sec->schedules as $sch) {
                                $items->push((object) [
                                    'class_section_code' => $sec->code,
                                    'course_code' => $sec->course->code,
                                    'course_name' => $sec->course->name,
                                    'credits' => $sec->course->credits,
                                    'lecturer_name' => $sec->lecturer?->user?->name ?? 'Chưa phân công',
                                    'room_name' => $sch->room?->code ?? 'Phòng học',
                                    'day_of_week' => (int) $sch->day_of_week,
                                    'start_period' => (int) $sch->start_period,
                                    'end_period' => (int) $sch->end_period,
                                    'periods_text' => "Tiết {$sch->start_period} - {$sch->end_period}",
                                    'time_text' => $this->getTimeText($sch->start_period, $sch->end_period),
                                    'card_color' => $this->pickColor($sec->course->code),
                                ]);
                            }
                        }

                        if ($items->isNotEmpty()) {
                            return $items;
                        }
                    }
                }
            } catch (Throwable) {
                // Fallback Mock
            }
        }

        return $this->getMockSchedules();
    }

    /**
     * Mock Schedules: lay danh sach lop tu session da dang ky o Phan 3 de nhat quan.
     *
     * @return Collection<int, object>
     */
    private function getMockSchedules(): Collection
    {
        $enrolledIds = session('mock_enrolled_section_ids', ['CS102.O11', 'SE201.O11']);

        $catalog = [
            'CS102.O11' => [
                [
                    'day_of_week' => 1, // Thu 2
                    'start_period' => 1,
                    'end_period' => 3,
                    'room_name' => 'Phòng A1.02',
                    'class_section_code' => 'CS102.O11',
                    'course_code' => 'CS102',
                    'course_name' => 'Kỹ thuật lập trình',
                    'credits' => 4,
                    'lecturer_name' => 'ThS. Hoàng Tuấn Anh',
                    'type' => 'Lý thuyết',
                ],
                [
                    'day_of_week' => 3, // Thu 4
                    'start_period' => 7,
                    'end_period' => 9,
                    'room_name' => 'Phòng Lab 2',
                    'class_section_code' => 'CS102.O11',
                    'course_code' => 'CS102',
                    'course_name' => 'Kỹ thuật lập trình (TH)',
                    'credits' => 4,
                    'lecturer_name' => 'ThS. Hoàng Tuấn Anh',
                    'type' => 'Thực hành',
                ],
            ],
            'SE201.O11' => [
                [
                    'day_of_week' => 3, // Thu 4
                    'start_period' => 1,
                    'end_period' => 3,
                    'room_name' => 'Phòng C1.01',
                    'class_section_code' => 'SE201.O11',
                    'course_code' => 'SE201',
                    'course_name' => 'Nhập môn Công nghệ phần mềm',
                    'credits' => 3,
                    'lecturer_name' => 'TS. Trần Văn Hùng',
                    'type' => 'Lý thuyết',
                ],
            ],
            'CS201.O11' => [
                [
                    'day_of_week' => 1, // Thu 2
                    'start_period' => 4,
                    'end_period' => 6,
                    'room_name' => 'Phòng B2.01',
                    'class_section_code' => 'CS201.O11',
                    'course_code' => 'CS201',
                    'course_name' => 'Cấu trúc dữ liệu và giải thuật',
                    'credits' => 4,
                    'lecturer_name' => 'TS. Nguyễn Minh Châu',
                    'type' => 'Lý thuyết',
                ],
                [
                    'day_of_week' => 5, // Thu 6
                    'start_period' => 1,
                    'end_period' => 3,
                    'room_name' => 'Phòng Lab 3',
                    'class_section_code' => 'CS201.O11',
                    'course_code' => 'CS201',
                    'course_name' => 'Cấu trúc dữ liệu & GT (TH)',
                    'credits' => 4,
                    'lecturer_name' => 'TS. Nguyễn Minh Châu',
                    'type' => 'Thực hành',
                ],
            ],
            'CS202.O11' => [
                [
                    'day_of_week' => 2, // Thu 3
                    'start_period' => 1,
                    'end_period' => 3,
                    'room_name' => 'Phòng A1.05',
                    'class_section_code' => 'CS202.O11',
                    'course_code' => 'CS202',
                    'course_name' => 'Cơ sở dữ liệu',
                    'credits' => 4,
                    'lecturer_name' => 'ThS. Đỗ Phương Thảo',
                    'type' => 'Lý thuyết',
                ],
                [
                    'day_of_week' => 4, // Thu 5
                    'start_period' => 4,
                    'end_period' => 6,
                    'room_name' => 'Phòng Lab 4',
                    'class_section_code' => 'CS202.O11',
                    'course_code' => 'CS202',
                    'course_name' => 'Cơ sở dữ liệu (TH)',
                    'credits' => 4,
                    'lecturer_name' => 'ThS. Đỗ Phương Thảo',
                    'type' => 'Thực hành',
                ],
            ],
            'SE205.O11' => [
                [
                    'day_of_week' => 5, // Thu 6
                    'start_period' => 4,
                    'end_period' => 6,
                    'room_name' => 'Phòng Lab 1',
                    'class_section_code' => 'SE205.O11',
                    'course_code' => 'SE205',
                    'course_name' => 'Lập trình Web căn bản',
                    'credits' => 3,
                    'lecturer_name' => 'ThS. Vũ Minh Trí',
                    'type' => 'Lý thuyết & TH',
                ],
            ],
            'MATH103.O11' => [
                [
                    'day_of_week' => 2, // Thu 3
                    'start_period' => 7,
                    'end_period' => 9,
                    'room_name' => 'Phòng B1.02',
                    'class_section_code' => 'MATH103.O11',
                    'course_code' => 'MATH103',
                    'course_name' => 'Giải tích 2',
                    'credits' => 3,
                    'lecturer_name' => 'TS. Phạm Thanh Sơn',
                    'type' => 'Lý thuyết',
                ],
            ],
            'ENG102.O11' => [
                [
                    'day_of_week' => 4, // Thu 5
                    'start_period' => 7,
                    'end_period' => 9,
                    'room_name' => 'Phòng A3.01',
                    'class_section_code' => 'ENG102.O11',
                    'course_code' => 'ENG102',
                    'course_name' => 'Tiếng Anh học thuật 2',
                    'credits' => 3,
                    'lecturer_name' => 'Cô Nguyễn Bích Thủy',
                    'type' => 'Lý thuyết',
                ],
            ],
        ];

        $results = collect();
        foreach ($enrolledIds as $secId) {
            if (isset($catalog[$secId])) {
                foreach ($catalog[$secId] as $item) {
                    $results->push((object) array_merge($item, [
                        'periods_text' => "Tiết {$item['start_period']} - {$item['end_period']}",
                        'time_text' => $this->getTimeText($item['start_period'], $item['end_period']),
                        'card_color' => $this->pickColor($item['course_code']),
                    ]));
                }
            }
        }

        return $results;
    }

    private function getTimeText(int $start, int $end): string
    {
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

        $s = $startTimes[$start] ?? '07:00';
        $e = $endTimes[$end] ?? '11:35';

        return "{$s} - {$e}";
    }

    private function pickColor(string $courseCode): string
    {
        $colors = [
            'CS101' => 'primary',
            'CS102' => 'primary',
            'CS201' => 'info',
            'CS202' => 'success',
            'SE201' => 'warning',
            'SE205' => 'danger',
            'MATH103' => 'secondary',
            'ENG102' => 'dark',
        ];

        return $colors[$courseCode] ?? 'primary';
    }
}

