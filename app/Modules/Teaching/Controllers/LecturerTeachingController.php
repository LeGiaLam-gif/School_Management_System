<?php

namespace App\Modules\Teaching\Controllers;

use App\Modules\Academic\Models\Semester;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Models\ClassSession;
use App\Modules\Teaching\Services\LecturerTeachingService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LecturerTeachingController
{
    public function __construct(
        private readonly LecturerTeachingService $teachingService,
    ) {}

    public function classes(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $semesters = $this->teachingService->getSemesters();
        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $semesterId = $this->teachingService->resolveSemesterId($requestedSemesterId);
        $keyword = $request->string('keyword')->trim()->toString();

        $classes = $this->teachingService->getAssignedClasses($user, $semesterId, $keyword);

        return view('teaching::lecturer.classes', [
            'semesters' => $semesters,
            'classes' => $classes,
            'filters' => [
                'semester_id' => $semesterId,
                'keyword' => $keyword,
            ],
        ]);
    }

    public function students(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $semesters = $this->teachingService->getSemesters();
        $requestedClassId = $request->filled('class_section_id') ? $request->integer('class_section_id') : null;
        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $keyword = $request->string('keyword')->trim()->toString();

        $selectedClass = null;
        if ($requestedClassId !== null) {
            $candidate = ClassSection::query()
                ->with(['course', 'semester'])
                ->find($requestedClassId);

            if ($candidate !== null && $candidate->isTaughtBy($user)) {
                $selectedClass = $candidate;
            }
        }

        $semesterId = $selectedClass !== null
            ? $selectedClass->semester_id
            : $this->teachingService->resolveSemesterId($requestedSemesterId);

        $classes = $this->teachingService->getAssignedClasses($user, $semesterId);

        if ($selectedClass === null) {
            $selectedClass = $classes->first();
        }

        $enrollments = $selectedClass !== null
            ? $this->teachingService->getClassEnrollments($selectedClass, $keyword)
            : collect();

        return view('teaching::lecturer.students', [
            'semesters' => $semesters,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'enrollments' => $enrollments,
            'filters' => [
                'semester_id' => $semesterId,
                'class_section_id' => $selectedClass?->id,
                'keyword' => $keyword,
            ],
        ]);
    }

    public function timetable(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $semesters = $this->teachingService->getSemesters();
        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $semesterId = $this->teachingService->resolveSemesterId($requestedSemesterId);

        /** @var Semester|null $selectedSemester */
        $selectedSemester = $semesterId !== null ? $semesters->firstWhere('id', $semesterId) : null;

        $rawWeekDate = $request->string('week_date')->trim()->toString();
        $today = CarbonImmutable::today();

        if ($rawWeekDate !== '') {
            try {
                $referenceDate = CarbonImmutable::parse($rawWeekDate);
            } catch (\Throwable) {
                $referenceDate = $today;
            }
        } elseif (
            $selectedSemester !== null
            && ($today->lt($selectedSemester->start_date) || $today->gt($selectedSemester->end_date))
        ) {
            $referenceDate = CarbonImmutable::parse($selectedSemester->start_date);
        } else {
            $referenceDate = $today;
        }

        $weekStart = $referenceDate->startOfWeek(CarbonInterface::MONDAY);
        $weekEnd = $referenceDate->endOfWeek(CarbonInterface::SUNDAY);

        $sessions = $this->teachingService->getWeeklySessions($user, $semesterId, $weekStart, $weekEnd);
        $schedules = $this->teachingService->getSemesterSchedules($user, $semesterId);

        $weekDays = [];
        for ($offset = 0; $offset < 7; $offset++) {
            $date = $weekStart->addDays($offset);
            $dateString = $date->toDateString();

            $weekDays[] = [
                'date' => $date,
                'date_string' => $dateString,
                'iso_day' => $date->dayOfWeekIso,
                'is_today' => $dateString === $today->toDateString(),
                'sessions' => $sessions->filter(
                    fn (ClassSession $session): bool => CarbonImmutable::parse($session->session_date)->toDateString() === $dateString
                )->values(),
            ];
        }

        return view('teaching::lecturer.timetable', [
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'prevWeekDate' => $weekStart->subWeek()->toDateString(),
            'nextWeekDate' => $weekStart->addWeek()->toDateString(),
            'todayDate' => $today->toDateString(),
            'weekDays' => $weekDays,
            'schedules' => $schedules,
            'filters' => [
                'semester_id' => $semesterId,
                'week_date' => $weekStart->toDateString(),
            ],
        ]);
    }
}
