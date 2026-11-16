<?php

namespace App\Modules\Attendance\Services;

use App\Modules\Academic\Models\Semester;
use App\Modules\Attendance\Events\AttendanceFinalized;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Support\AbsenceRateCalculator;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(private readonly AbsenceRateCalculator $absence) {}

    /**
     * Danh sach hoc ky sap xep uu tien hoc ky hien tai.
     *
     * @return Collection<int, Semester>
     */
    public function getSemesters(): Collection
    {
        return Semester::query()
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();
    }

    /**
     * Xac dinh hoc ky mac dinh neu nguoi dung chua chon tren bo loc.
     */
    public function resolveSemesterId(?int $requestedSemesterId): ?int
    {
        if ($requestedSemesterId !== null) {
            return $requestedSemesterId;
        }

        $currentId = Semester::query()
            ->where('is_current', true)
            ->value('id');

        if (is_numeric($currentId)) {
            return (int) $currentId;
        }

        $latestId = Semester::query()
            ->orderByDesc('start_date')
            ->value('id');

        return is_numeric($latestId) ? (int) $latestId : null;
    }

    /**
     * Danh sach lop hoc phan ma giang vien phu trach trong hoc ky.
     *
     * @return Collection<int, ClassSection>
     */
    public function getLecturerClasses(User $user, ?int $semesterId): Collection
    {
        $lecturerId = $user->lecturer?->id;

        if ($lecturerId === null) {
            return new Collection;
        }

        $query = ClassSection::query()
            ->with(['course', 'semester'])
            ->where('lecturer_id', $lecturerId);

        if ($semesterId !== null) {
            $query->where('semester_id', $semesterId);
        }

        return $query->orderBy('code')->get();
    }

    /**
     * Danh sach cac buoi hoc cua mot lop hoc phan.
     *
     * @return Collection<int, ClassSession>
     */
    public function getClassSessions(ClassSection $classSection): Collection
    {
        return ClassSession::query()
            ->with('room')
            ->withCount('attendanceRecords')
            ->where('class_section_id', $classSection->id)
            ->orderBy('session_no')
            ->orderBy('session_date')
            ->get();
    }

    /**
     * Tong hop danh sach sinh vien, ban ghi diem danh cua buoi chon va chuyen can tich luy.
     *
     * @return array<int, array{
     *     enrollment: Enrollment,
     *     record: AttendanceRecord|null,
     *     status: AttendanceStatus,
     *     note: string,
     *     summary: array{held: int, absent: int, late: int, excused: int, rate: float, exceeds_threshold: bool}
     * }>
     */
    public function getSessionAttendanceRows(ClassSection $classSection, ?ClassSession $session): array
    {
        $enrollments = Enrollment::query()
            ->active()
            ->with(['student.user', 'student.administrativeClass'])
            ->where('class_section_id', $classSection->id)
            ->get()
            ->sortBy(function (Enrollment $enrollment): string {
                $student = $enrollment->student;

                return $student instanceof Student ? $student->student_code : '';
            })
            ->values();

        /** @var Collection<int, AttendanceRecord> $recordsByEnrollment */
        $recordsByEnrollment = $session !== null
            ? AttendanceRecord::query()
                ->where('class_session_id', $session->id)
                ->get()
                ->keyBy('enrollment_id')
            : new Collection;

        $rows = [];
        foreach ($enrollments as $enrollment) {
            /** @var AttendanceRecord|null $record */
            $record = $recordsByEnrollment->get($enrollment->id);

            $rows[] = [
                'enrollment' => $enrollment,
                'record' => $record,
                'status' => $record !== null ? $record->status : AttendanceStatus::Present,
                'note' => $record !== null ? ($record->note ?? '') : '',
                'summary' => $this->summaryForEnrollment($enrollment),
            ];
        }

        return $rows;
    }

    /**
     * Luu ket qua diem danh cho mot buoi hoc (su dung Eloquent model de kich hoat Auditable).
     *
     * @param  array<int|string, array{status: string, note?: string|null}>  $entries
     */
    public function markSessionAttendance(
        ClassSession $session,
        User $actor,
        array $entries,
        SessionStatus $targetSessionStatus = SessionStatus::Held,
    ): void {
        DB::transaction(function () use ($session, $actor, $entries, $targetSessionStatus): void {
            /** @var array<int, int> $validEnrollmentIds */
            $validEnrollmentIds = Enrollment::query()
                ->active()
                ->where('class_section_id', $session->class_section_id)
                ->pluck('id')
                ->map(fn (mixed $id): int => is_numeric($id) ? (int) $id : 0)
                ->all();

            $validMap = array_flip($validEnrollmentIds);
            $now = now();

            foreach ($entries as $enrollmentId => $entry) {
                $id = (int) $enrollmentId;

                if (! isset($validMap[$id])) {
                    continue;
                }

                $status = AttendanceStatus::from($entry['status']);
                $rawNote = isset($entry['note']) ? trim($entry['note']) : '';
                $note = $rawNote !== '' ? $rawNote : null;

                AttendanceRecord::query()->updateOrCreate(
                    [
                        'class_session_id' => $session->id,
                        'enrollment_id' => $id,
                    ],
                    [
                        'status' => $status,
                        'note' => $note,
                        'marked_by' => $actor->id,
                        'marked_at' => $now,
                    ],
                );
            }

            if ($session->status !== $targetSessionStatus) {
                $session->update(['status' => $targetSessionStatus]);
            }

            AttendanceFinalized::dispatch($session);
        });
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
