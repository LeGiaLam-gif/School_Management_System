<?php

namespace App\Modules\Teaching\Database\Seeders;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Room;
use App\Modules\Academic\Models\Semester;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Teaching\Models\ClassSchedule;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\SessionStatus;
use Illuminate\Database\Seeder;

/**
 * Lop hoc phan mau (chuyen tu CourseSectionSeeder cua repo cu). Khac biet so voi repo cu:
 * moi lop co lich o bang rieng (class_schedules) va cac buoi hoc cu the (class_sessions);
 * day_of_week theo ISO-8601 (repo cu: 2..8 voi 8 = Chu nhat -> tru 1).
 */
class TeachingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $lecturers = Lecturer::orderBy('id')->get();
        $old = Semester::where('code', '2025-1')->firstOrFail();
        $current = Semester::where('code', '2026-1')->firstOrFail();

        // [ma hoc phan, hoc ky, thu (ISO), tiet bat dau, tiet ket thuc, phong]
        $plan = [
            ['IT101', $old, 1, 1, 3, 'A101'],
            ['IT102', $old, 2, 4, 6, 'A102'],
            ['IT201', $current, 3, 1, 3, 'B201'],
            ['IT202', $current, 4, 4, 6, 'B202'],
            ['IT301', $current, 5, 1, 2, 'C301'],
        ];

        foreach ($plan as $i => [$courseCode, $semester, $day, $start, $end, $roomCode]) {
            $course = Course::where('code', $courseCode)->firstOrFail();
            $room = Room::where('code', $roomCode)->firstOrFail();
            $isCurrent = $semester->is_current;

            $section = ClassSection::firstOrCreate(
                ['course_id' => $course->id, 'semester_id' => $semester->id, 'code' => $courseCode.'.01'],
                [
                    'lecturer_id' => $lecturers[$i % $lecturers->count()]->id,
                    'capacity' => 40,
                    'status' => $isCurrent ? ClassSectionStatus::Open : ClassSectionStatus::GradesPublished,
                ]
            );

            $schedule = ClassSchedule::firstOrCreate(
                ['class_section_id' => $section->id, 'day_of_week' => $day, 'start_period' => $start],
                [
                    'room_id' => $room->id,
                    'end_period' => $end,
                    'start_date' => $semester->start_date,
                    'end_date' => $semester->end_date,
                ]
            );

            // 3 buoi gan day (da dien ra) de co du lieu diem danh.
            $dates = $isCurrent
                ? [now()->subDays(14), now()->subDays(7), now()]
                : [$semester->start_date->copy(), $semester->start_date->copy()->addDays(7), $semester->start_date->copy()->addDays(14)];

            foreach ($dates as $n => $date) {
                ClassSession::firstOrCreate(
                    ['class_section_id' => $section->id, 'session_no' => $n + 1],
                    [
                        'class_schedule_id' => $schedule->id,
                        'session_date' => $date->toDateString(),
                        'start_period' => $start,
                        'end_period' => $end,
                        'room_id' => $room->id,
                        'status' => SessionStatus::Held,
                    ]
                );
            }
        }
    }
}
