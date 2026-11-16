<?php

namespace App\Modules\Grading\Controllers;

use App\Modules\Grading\Policies\GradePolicy;
use App\Modules\Grading\Requests\PublishSectionGradesRequest;
use App\Modules\Grading\Requests\SaveSectionGradesRequest;
use App\Modules\Grading\Requests\UpdateGradeComponentsRequest;
use App\Modules\Grading\Services\GradingService;
use App\Modules\Grading\Support\GradeCalculator;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LecturerGradeController
{
    public function __construct(
        private readonly GradingService $gradingService,
        private readonly GradeCalculator $calculator,
        private readonly GradePolicy $gradePolicy,
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $semesters = $this->gradingService->getSemesters();
        $requestedClassId = $request->filled('class_section_id') ? $request->integer('class_section_id') : null;
        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;

        $selectedClass = null;
        if ($requestedClassId !== null) {
            $candidateClass = ClassSection::query()
                ->with(['course', 'semester'])
                ->find($requestedClassId);

            if ($candidateClass !== null && $candidateClass->isTaughtBy($user)) {
                $selectedClass = $candidateClass;
            }
        }

        $semesterId = $selectedClass !== null
            ? $selectedClass->semester_id
            : $this->gradingService->resolveSemesterId($requestedSemesterId);

        $classes = $this->gradingService->getLecturerClasses($user, $semesterId);

        if ($selectedClass === null) {
            $selectedClass = $classes->first();
        }

        $components = $selectedClass !== null
            ? $this->gradingService->getSectionComponents($selectedClass)
            : collect();

        $rows = $selectedClass !== null
            ? $this->gradingService->getSectionGradeRows($selectedClass, $components)
            : [];

        $canManage = $selectedClass !== null && $this->gradePolicy->manage($user, $selectedClass);
        $canPublish = $selectedClass !== null
            && $this->gradePolicy->publish($user, $selectedClass)
            && $selectedClass->status !== ClassSectionStatus::GradesPublished;

        $weightsValid = $components->isNotEmpty()
            && $this->calculator->weightsSumTo100($components->pluck('weight')->all());

        $completedCount = 0;
        $passedCount = 0;
        foreach ($rows as $row) {
            if ($row['finalResult'] !== null) {
                $completedCount++;
                if ($row['finalResult']->is_passed) {
                    $passedCount++;
                }
            }
        }

        return view('grading::lecturer.index', [
            'semesters' => $semesters,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'components' => $components,
            'rows' => $rows,
            'canManage' => $canManage,
            'canPublish' => $canPublish,
            'weightsValid' => $weightsValid,
            'stats' => [
                'total' => count($rows),
                'completed' => $completedCount,
                'passed' => $passedCount,
            ],
            'filters' => [
                'semester_id' => $semesterId,
                'class_section_id' => $selectedClass?->id,
            ],
        ]);
    }

    public function saveGrades(SaveSectionGradesRequest $request, ClassSection $classSection): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array<int|string, array<int|string, float|int|string|null>> $grades */
        $grades = $request->validated('grades', []);

        $this->gradingService->saveSectionGrades($classSection, $user, $grades);

        return redirect()
            ->route('lecturer.grades', [
                'semester_id' => $classSection->semester_id,
                'class_section_id' => $classSection->id,
            ])
            ->with('status', "Đã lưu bảng điểm lớp {$classSection->code} thành công.");
    }

    public function saveComponents(UpdateGradeComponentsRequest $request, ClassSection $classSection): RedirectResponse
    {
        /** @var array<int, array{id?: int|string|null, name: string, weight: float|int|string, max_score: float|int|string}> $components */
        $components = $request->validated('components', []);

        $this->gradingService->syncSectionComponents($classSection, $components);

        return redirect()
            ->route('lecturer.grades', [
                'semester_id' => $classSection->semester_id,
                'class_section_id' => $classSection->id,
            ])
            ->with('status', "Đã cập nhật cấu trúc cột điểm cho lớp {$classSection->code}.");
    }

    public function publish(PublishSectionGradesRequest $request, ClassSection $classSection): RedirectResponse
    {
        $this->gradingService->publishSectionGrades($classSection);

        return redirect()
            ->route('lecturer.grades', [
                'semester_id' => $classSection->semester_id,
                'class_section_id' => $classSection->id,
            ])
            ->with('status', "Đã công bố và khóa bảng điểm lớp {$classSection->code}.");
    }
}
