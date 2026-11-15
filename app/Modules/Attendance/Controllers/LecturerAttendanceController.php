<?php

namespace App\Modules\Attendance\Controllers;

use App\Modules\Attendance\Requests\StoreSessionAttendanceRequest;
use App\Modules\Attendance\Services\AttendanceService;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\SessionStatus;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LecturerAttendanceController
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $semesters = $this->attendanceService->getSemesters();
        $requestedClassId = $request->filled('class_section_id') ? $request->integer('class_section_id') : null;
        $requestedSessionId = $request->filled('class_session_id') ? $request->integer('class_session_id') : null;
        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;

        $selectedSession = null;
        $selectedClass = null;

        if ($requestedSessionId !== null) {
            $candidateSession = ClassSession::query()
                ->with(['classSection.course', 'classSection.semester', 'room'])
                ->find($requestedSessionId);

            $candidateSection = $candidateSession?->classSection;
            if ($candidateSession !== null && $candidateSection instanceof ClassSection && $candidateSection->isTaughtBy($user)) {
                $selectedSession = $candidateSession;
                $selectedClass = $candidateSection;
            }
        }

        if ($selectedClass === null && $requestedClassId !== null) {
            $candidateClass = ClassSection::query()
                ->with(['course', 'semester'])
                ->find($requestedClassId);

            if ($candidateClass !== null && $candidateClass->isTaughtBy($user)) {
                $selectedClass = $candidateClass;
            }
        }

        $semesterId = $selectedClass !== null
            ? $selectedClass->semester_id
            : $this->attendanceService->resolveSemesterId($requestedSemesterId);

        $classes = $this->attendanceService->getLecturerClasses($user, $semesterId);

        if ($selectedClass === null) {
            $selectedClass = $classes->first();
        }

        $sessions = $selectedClass !== null
            ? $this->attendanceService->getClassSessions($selectedClass)
            : collect();

        if ($selectedSession === null && $sessions->isNotEmpty()) {
            $today = CarbonImmutable::today();
            $selectedSession = $sessions->first(
                fn (ClassSession $session): bool => CarbonImmutable::parse($session->session_date)->isSameDay($today)
            )
                ?? $sessions->last(fn (ClassSession $session): bool => $session->status === SessionStatus::Held)
                ?? $sessions->first();
        }

        $rows = $selectedClass !== null
            ? $this->attendanceService->getSessionAttendanceRows($selectedClass, $selectedSession)
            : [];

        $statusCounts = [
            AttendanceStatus::Present->value => 0,
            AttendanceStatus::Late->value => 0,
            AttendanceStatus::Excused->value => 0,
            AttendanceStatus::Absent->value => 0,
        ];

        foreach ($rows as $row) {
            $statusCounts[$row['status']->value]++;
        }

        return view('attendance::lecturer.index', [
            'semesters' => $semesters,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'sessions' => $sessions,
            'selectedSession' => $selectedSession,
            'rows' => $rows,
            'statusCounts' => $statusCounts,
            'statuses' => AttendanceStatus::cases(),
            'sessionStatuses' => SessionStatus::cases(),
            'filters' => [
                'semester_id' => $semesterId,
                'class_section_id' => $selectedClass?->id,
                'class_session_id' => $selectedSession?->id,
            ],
        ]);
    }

    public function store(StoreSessionAttendanceRequest $request, ClassSession $classSession): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array<int|string, array{status: string, note?: string|null}> $records */
        $records = $request->validated('records', []);

        $rawSessionStatus = $request->validated('session_status');
        $targetSessionStatus = is_string($rawSessionStatus) && $rawSessionStatus !== ''
            ? SessionStatus::from($rawSessionStatus)
            : SessionStatus::Held;

        $this->attendanceService->markSessionAttendance($classSession, $user, $records, $targetSessionStatus);

        $section = $classSession->classSection;
        $semesterId = $section instanceof ClassSection ? $section->semester_id : null;

        return redirect()
            ->route('lecturer.attendance', [
                'semester_id' => $semesterId,
                'class_section_id' => $classSession->class_section_id,
                'class_session_id' => $classSession->id,
            ])
            ->with('status', "Đã lưu điểm danh buổi #{$classSession->session_no} thành công.");
    }
}
