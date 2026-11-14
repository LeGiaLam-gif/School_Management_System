<?php

namespace App\Modules\Grading\Controllers;

use App\Modules\Grading\Services\StudentGradeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentGradeController extends Controller
{
    public function __construct(
        private readonly StudentGradeService $service
    ) {}

    /**
     * Xem Bang diem & GPA cua sinh vien theo hoc ky hoac toan khoa.
     */
    public function index(Request $request): View
    {
        $selectedSemester = $request->string('semester')->trim()->toString();

        $report = $this->service->getGradeReport(
            $request->user(),
            $selectedSemester ?: null
        );

        return view('grading::student.grades', [
            'report' => $report,
        ]);
    }
}

