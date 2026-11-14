<?php

namespace App\Modules\Teaching\Controllers;

use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Services\LecturerTeachingService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class LecturerTeachingController extends Controller
{
    public function __construct(
        private readonly LecturerTeachingService $service
    ) {}

    /**
     * Danh sach lop hoc phan duoc phan cong giang day.
     */
    public function classes(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $semesterId = $this->service->resolveSemesterId($requestedSemesterId);
        $keyword = $request->string('keyword')->trim()->toString();

        $semesters = $this->service->getSemesters();
        $classes = $this->service->getAssignedClasses($user, $semesterId, $keyword);

        return view('teaching::lecturer.classes', [
            'semesters' => $semesters,
            'classes' => $classes,
            'filters' => [
                'semester_id' => $semesterId,
                'keyword' => $keyword,
            ],
        ]);
    }

    /**
     * Danh sach sinh vien theo lop hoc phan duoc phan cong.
     */
    public function students(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $classSectionId = $request->filled('class_section_id') ? $request->integer('class_section_id') : null;
        $keyword = $request->string('keyword')->trim()->toString();

        $selectedClass = null;
        if ($classSectionId !== null) {
            $selectedClass = ClassSection::query()
                ->with(['course.department', 'semester'])
                ->findOrFail($classSectionId);

            Gate::authorize('view', $selectedClass);
            $requestedSemesterId = $selectedClass->semester_id;
        }

        $semesterId = $this->service->resolveSemesterId($requestedSemesterId);
        $semesters = $this->service->getSemesters();
        $classes = $this->service->getAssignedClasses($user, $semesterId);

        if ($selectedClass === null) {
            $selectedClass = $classes->first();
        }

        $enrollments = $selectedClass !== null
            ? $this->service->getClassEnrollments($selectedClass, $keyword)
            : new Collection;

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
}
