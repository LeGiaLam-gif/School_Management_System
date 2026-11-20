<?php

namespace App\Modules\Reporting\Services;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Semester;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Support\AbsenceRateCalculator;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Collection;

class LecturerReportService
{
    public function __construct(
        private readonly AbsenceRateCalculator $absenceCalculator,
    ) {}

    /**
     * @return Collection<int, Semester>
     */
    public function getSemesters(): Collection
    {
        return Semester::query()
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();
    }

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
     * @param  Collection<int, ClassSection>  $classes
     * @return array{
     *     thresholdPercent: float,
     *     overview: array{
     *         classCount: int,
     *         studentCount: int,
     *         heldSessions: int,
     *         totalSessions: int,
     *         attendanceRate: float|null,
     *         gradedCount: int,
     *         passedCount: int,
     *         passRate: float|null,
     *         averageScore: float|null,
     *         warningCount: int
     *     },
     *     classSummaries: list<array{
     *         section: ClassSection,
     *         studentCount: int,
     *         heldSessions: int,
     *         totalSessions: int,
     *         attendanceRate: float|null,
     *         gradedCount: int,
     *         passedCount: int,
     *         passRate: float|null,
     *         averageScore: float|null,
     *         warningCount: int
     *     }>,
     *     gradeDistribution: array<string, int>,
     *     attendanceWarnings: list<array{
     *         studentCode: string,
     *         studentName: string,
     *         classCode: string,
     *         courseName: string,
     *         absentCount: int,
     *         heldSessions: int,
     *         absenceRatePercent: float,
     *         totalScore: float|null,
     *         letterGrade: string|null
     *     }>
     * }
     */
    public function buildReport(Collection $classes, ?int $selectedClassId = null): array
    {
        $threshold = (float) config('sms.attendance.absence_threshold', 0.20);
        $letters = ['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'];
        $gradeDistribution = array_fill_keys($letters, 0);

        $targetClasses = $selectedClassId !== null
            ? $classes->where('id', $selectedClassId)->values()
            : $classes;

        $targetClassIds = $targetClasses->pluck('id')->all();

        if ($targetClassIds === []) {
            return [
                'thresholdPercent' => round($threshold * 100, 1),
                'overview' => [
                    'classCount' => 0,
                    'studentCount' => 0,
                    'heldSessions' => 0,
                    'totalSessions' => 0,
                    'attendanceRate' => null,
                    'gradedCount' => 0,
                    'passedCount' => 0,
                    'passRate' => null,
                    'averageScore' => null,
                    'warningCount' => 0,
                ],
                'classSummaries' => [],
                'gradeDistribution' => $gradeDistribution,
                'attendanceWarnings' => [],
            ];
        }

        $enrollments = Enrollment::query()
            ->whereIn('class_section_id', $targetClassIds)
            ->where('status', '!=', EnrollmentStatus::Dropped->value)
            ->with(['student.user', 'classSection.course'])
            ->get();

        $sessions = ClassSession::query()
            ->whereIn('class_section_id', $targetClassIds)
            ->get();

        $heldSessionIds = $sessions
            ->filter(fn (ClassSession $session): bool => $session->status === SessionStatus::Held)
            ->pluck('id')
            ->all();

        $enrollmentIds = $enrollments->pluck('id')->all();

        $absentCountsByEnrollment = AttendanceRecord::query()
            ->whereIn('class_session_id', $heldSessionIds)
            ->whereIn('enrollment_id', $enrollmentIds)
            ->where('status', AttendanceStatus::Absent->value)
            ->selectRaw('enrollment_id, COUNT(*) as aggregate')
            ->groupBy('enrollment_id')
            ->pluck('aggregate', 'enrollment_id');

        $finalResultsByEnrollment = FinalResult::query()
            ->whereIn('enrollment_id', $enrollmentIds)
            ->get()
            ->keyBy('enrollment_id');

        $enrollmentsBySection = $enrollments->groupBy('class_section_id');
        $sessionsBySection = $sessions->groupBy('class_section_id');

        $classSummaries = [];
        /** @var list<array{studentCode: string, studentName: string, classCode: string, courseName: string, absentCount: int, heldSessions: int, absenceRatePercent: float, totalScore: float|null, letterGrade: string|null}> $attendanceWarnings */
        $attendanceWarnings = [];

        $totalStudents = 0;
        $totalHeldSessions = 0;
        $totalAllSessions = 0;
        $totalExpectedAttendanceSlots = 0;
        $totalAbsentSlots = 0;
        $totalGraded = 0;
        $totalPassed = 0;
        $totalScoreSum = 0.0;

        foreach ($targetClasses as $section) {
            /** @var Collection<int, Enrollment> $sectionEnrollments */
            $sectionEnrollments = $enrollmentsBySection->get($section->id, new Collection);
            /** @var Collection<int, ClassSession> $sectionSessions */
            $sectionSessions = $sessionsBySection->get($section->id, new Collection);

            $studentCount = $sectionEnrollments->count();
            $allSessionsCount = $sectionSessions->count();
            $heldSessionsCount = $sectionSessions
                ->filter(fn (ClassSession $session): bool => $session->status === SessionStatus::Held)
                ->count();

            $sectionAbsentSlots = 0;
            $sectionGraded = 0;
            $sectionPassed = 0;
            $sectionScoreSum = 0.0;
            $sectionWarnings = 0;

            $course = $section->course;
            $courseName = $course->name;

            foreach ($sectionEnrollments as $enrollment) {
                $absentCount = (int) ($absentCountsByEnrollment->get($enrollment->id) ?? 0);
                $sectionAbsentSlots += $absentCount;

                /** @var FinalResult|null $finalResult */
                $finalResult = $finalResultsByEnrollment->get($enrollment->id);

                if ($finalResult !== null) {
                    $sectionGraded++;
                    $sectionScoreSum += $finalResult->total_score;
                    if ($finalResult->is_passed) {
                        $sectionPassed++;
                    }
                    if (array_key_exists($finalResult->letter_grade, $gradeDistribution)) {
                        $gradeDistribution[$finalResult->letter_grade]++;
                    }
                }

                if ($heldSessionsCount > 0 && $this->absenceCalculator->exceeds($absentCount, $heldSessionsCount, $threshold)) {
                    $sectionWarnings++;
                    $student = $enrollment->student;
                    $studentUser = $student instanceof Student ? $student->user : null;
                    $rate = $this->absenceCalculator->rate($absentCount, $heldSessionsCount);

                    $attendanceWarnings[] = [
                        'studentCode' => $student instanceof Student ? $student->student_code : '—',
                        'studentName' => $studentUser instanceof User ? $studentUser->name : '—',
                        'classCode' => $section->code,
                        'courseName' => $courseName,
                        'absentCount' => $absentCount,
                        'heldSessions' => $heldSessionsCount,
                        'absenceRatePercent' => round($rate * 100, 1),
                        'totalScore' => $finalResult?->total_score,
                        'letterGrade' => $finalResult?->letter_grade,
                    ];
                }
            }

            $expectedSlots = $studentCount * $heldSessionsCount;
            $sectionAttendanceRate = $expectedSlots > 0
                ? round((($expectedSlots - $sectionAbsentSlots) / $expectedSlots) * 100, 1)
                : null;

            $sectionPassRate = $sectionGraded > 0
                ? round(($sectionPassed / $sectionGraded) * 100, 1)
                : null;

            $sectionAvgScore = $sectionGraded > 0
                ? round($sectionScoreSum / $sectionGraded, 2)
                : null;

            $classSummaries[] = [
                'section' => $section,
                'studentCount' => $studentCount,
                'heldSessions' => $heldSessionsCount,
                'totalSessions' => $allSessionsCount,
                'attendanceRate' => $sectionAttendanceRate,
                'gradedCount' => $sectionGraded,
                'passedCount' => $sectionPassed,
                'passRate' => $sectionPassRate,
                'averageScore' => $sectionAvgScore,
                'warningCount' => $sectionWarnings,
            ];

            $totalStudents += $studentCount;
            $totalHeldSessions += $heldSessionsCount;
            $totalAllSessions += $allSessionsCount;
            $totalExpectedAttendanceSlots += $expectedSlots;
            $totalAbsentSlots += $sectionAbsentSlots;
            $totalGraded += $sectionGraded;
            $totalPassed += $sectionPassed;
            $totalScoreSum += $sectionScoreSum;
        }

        usort(
            $attendanceWarnings,
            static fn (array $a, array $b): int => $b['absenceRatePercent'] <=> $a['absenceRatePercent']
        );

        return [
            'thresholdPercent' => round($threshold * 100, 1),
            'overview' => [
                'classCount' => $targetClasses->count(),
                'studentCount' => $totalStudents,
                'heldSessions' => $totalHeldSessions,
                'totalSessions' => $totalAllSessions,
                'attendanceRate' => $totalExpectedAttendanceSlots > 0
                    ? round((($totalExpectedAttendanceSlots - $totalAbsentSlots) / $totalExpectedAttendanceSlots) * 100, 1)
                    : null,
                'gradedCount' => $totalGraded,
                'passedCount' => $totalPassed,
                'passRate' => $totalGraded > 0 ? round(($totalPassed / $totalGraded) * 100, 1) : null,
                'averageScore' => $totalGraded > 0 ? round($totalScoreSum / $totalGraded, 2) : null,
                'warningCount' => count($attendanceWarnings),
            ],
            'classSummaries' => $classSummaries,
            'gradeDistribution' => $gradeDistribution,
            'attendanceWarnings' => $attendanceWarnings,
        ];
    }
}
