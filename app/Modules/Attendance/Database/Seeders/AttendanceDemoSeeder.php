<?php

namespace App\Modules\Attendance\Database\Seeders;

use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use Illuminate\Database\Seeder;

/**
 * Diem danh mau cho 3 buoi/lop (chuyen tu CourseSectionSeeder cu, nay theo buoi hoc cu the).
 */
class AttendanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $pattern = [
            AttendanceStatus::Present,
            AttendanceStatus::Present,
            AttendanceStatus::Late,
            AttendanceStatus::Absent,
        ];

        $sessions = ClassSession::with('classSection.lecturer')->orderBy('id')->get();

        foreach ($sessions as $session) {
            $enrollments = Enrollment::active()
                ->where('class_section_id', $session->class_section_id)
                ->orderBy('id')
                ->get();

            foreach ($enrollments as $s => $enrollment) {
                AttendanceRecord::firstOrCreate(
                    ['class_session_id' => $session->id, 'enrollment_id' => $enrollment->id],
                    [
                        'status' => $pattern[($s + $session->session_no) % 4],
                        'marked_by' => $session->classSection->lecturer?->user_id,
                        'marked_at' => $session->session_date,
                    ]
                );
            }
        }
    }
}
