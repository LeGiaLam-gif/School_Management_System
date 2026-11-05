<?php

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Support\AbsenceRateCalculator;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\SessionStatus;

/**
 * Con thieu (Sprint 4): markSession (ghi hang loat kieu upsert + audit thu cong),
 * finalizeSession, cua so cho phep +/-7 ngay, Listener CheckAbsenceThreshold.
 */
class AttendanceService
{
    public function __construct(private readonly AbsenceRateCalculator $absence)
    {
    }

    /**
     * Thong ke chuyen can cua mot dang ky (FR-ATT-02).
     *
     * @return array{held: int, absent: int, late: int, excused: int, rate: float, exceeds_threshold: bool}
     */
    public function summaryForEnrollment(Enrollment $enrollment): array
    {
        $held = ClassSession::query()
            ->where('class_section_id', $enrollment->class_section_id)
            ->where('status', SessionStatus::Held->value)
            ->count();

        $counts = AttendanceRecord::query()
            ->where('enrollment_id', $enrollment->id)
            ->whereHas('classSession', fn ($q) => $q->where('status', SessionStatus::Held->value))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $absent = (int) ($counts[AttendanceStatus::Absent->value] ?? 0);

        return [
            'held' => $held,
            'absent' => $absent,
            'late' => (int) ($counts[AttendanceStatus::Late->value] ?? 0),
            'excused' => (int) ($counts[AttendanceStatus::Excused->value] ?? 0),
            'rate' => $this->absence->rate($absent, $held),
            'exceeds_threshold' => $this->absence->exceeds($absent, $held, (float) config('sms.attendance.absence_threshold')),
        ];
    }
}
