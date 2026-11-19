<?php

namespace App\Modules\Academic\Controllers\Admin;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Requests\Admin\StoreCourseRequest;
use App\Modules\Academic\Requests\Admin\UpdateCourseRequest;
use App\Modules\Academic\Services\CourseService;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with(['department', 'prerequisites'])->withCount('classSections');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($credits = $request->input('credits')) {
            $query->where('credits', $credits);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        $courses = $query->orderBy('code')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $stats = [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_credits' => (int) Course::sum('credits'),
            'courses_with_prereq' => DB::table('course_prerequisites')->distinct('course_id')->count('course_id'),
        ];

        return view('academic::admin.courses.index', compact('courses', 'departments', 'stats'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $availableCourses = Course::where('is_active', true)->orderBy('code')->get();

        return view('academic::admin.courses.create', compact('departments', 'availableCourses'));
    }

    public function store(StoreCourseRequest $request, CourseService $courseService): RedirectResponse
    {
        $validated = $request->validated();
        $prerequisiteIds = $validated['prerequisite_ids'] ?? [];
        unset($validated['prerequisite_ids']);

        try {
            $course = Course::create($validated);

            if (! empty($prerequisiteIds)) {
                $courseService->setPrerequisites($course, $prerequisiteIds);
            }

            return redirect()
                ->route('admin.courses.show', $course)
                ->with('success', "Đã tạo học phần {$course->name} ({$course->code}) thành công.");
        } catch (BusinessRuleException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Course $course): View
    {
        $course->load([
            'department',
            'prerequisites',
            'requiredBy',
            'classSections.semester',
            'classSections.lecturer.user',
        ]);

        return view('academic::admin.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $course->load('prerequisites');
        $departments = Department::orderBy('name')->get();
        $availableCourses = Course::where('id', '!=', $course->id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('academic::admin.courses.edit', compact('course', 'departments', 'availableCourses'));
    }

    public function update(UpdateCourseRequest $request, Course $course, CourseService $courseService): RedirectResponse
    {
        $validated = $request->validated();
        $prerequisiteIds = $validated['prerequisite_ids'] ?? [];
        unset($validated['prerequisite_ids']);

        try {
            $course->update($validated);
            $courseService->setPrerequisites($course, $prerequisiteIds);

            return redirect()
                ->route('admin.courses.show', $course)
                ->with('success', "Đã cập nhật học phần {$course->code} thành công.");
        } catch (BusinessRuleException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Course $course, CourseService $courseService): RedirectResponse
    {
        if ($course->requiredBy()->exists()) {
            $dependents = $course->requiredBy->pluck('code')->implode(', ');

            return back()->with('error', "Không thể xóa học phần {$course->code} vì đang là môn tiên quyết cho: {$dependents}.");
        }

        try {
            $code = $course->code;
            $courseService->delete($course);

            return redirect()
                ->route('admin.courses.index')
                ->with('success', "Đã xóa học phần {$code} thành công.");
        } catch (BusinessRuleException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
