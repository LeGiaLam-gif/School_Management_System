<?php

namespace App\Modules\Academic\Controllers;

use App\Modules\Academic\Services\StudentCurriculumService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentCurriculumController extends Controller
{
    public function __construct(
        private readonly StudentCurriculumService $service
    ) {}

    /**
     * Xem Khung chuong trinh dao tao theo nganh cua sinh vien.
     */
    public function curriculum(Request $request): View
    {
        $curriculum = $this->service->getCurriculum($request->user());

        return view('academic::student.curriculum', [
            'curriculum' => $curriculum,
        ]);
    }

    /**
     * Tra cuu danh muc tat ca cac mon hoc toan truong.
     */
    public function courses(Request $request): View
    {
        $filters = [
            'keyword' => $request->string('keyword')->trim()->toString(),
            'department' => $request->string('department')->trim()->toString(),
            'credits' => $request->filled('credits') ? $request->integer('credits') : null,
        ];

        $courses = $this->service->getCourses($filters);

        return view('academic::student.courses', [
            'courses' => $courses,
            'filters' => $filters,
        ]);
    }

    /**
     * Xem chi tiet hoc phan va dieu kien tien quyet.
     */
    public function courseDetail(string $code): View|RedirectResponse
    {
        $course = $this->service->getCourseDetail($code);

        if ($course === null) {
            return redirect()->route('student.courses')
                ->with('error', "Không tìm thấy thông tin cho học phần mã {$code}.");
        }

        return view('academic::student.course_detail', [
            'course' => $course,
        ]);
    }
}
