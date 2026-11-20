<?php

namespace App\Modules\Teaching\Controllers\Admin;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Semester;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Requests\Admin\StoreClassSectionRequest;
use App\Modules\Teaching\Requests\Admin\UpdateClassSectionRequest;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ClassSectionController extends Controller
{
    public function index(Request $request): View
    {
        $query = ClassSection::with(['course.department', 'semester', 'lecturer.user'])
            ->withCount('enrollments');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lecturer.user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($semesterId = $request->input('semester_id')) {
            $query->where('semester_id', $semesterId);
        }

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($lecturerId = $request->input('lecturer_id')) {
            if ($lecturerId === 'unassigned') {
                $query->whereNull('lecturer_id');
            } else {
                $query->where('lecturer_id', $lecturerId);
            }
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $classSections = $query->orderBy('semester_id', 'desc')
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $currentSemester = Semester::where('is_current', true)->first();
        $courses = Course::where('is_active', true)->orderBy('code')->get();
        $lecturers = Lecturer::with('user')->get()->sortBy('user.name');

        $stats = [
            'total_sections' => ClassSection::count(),
            'open_sections' => ClassSection::where('status', ClassSectionStatus::Open->value)->count(),
            'in_progress_sections' => ClassSection::where('status', ClassSectionStatus::InProgress->value)->count(),
            'total_enrolled' => (int) ClassSection::sum('enrolled_count'),
            'unassigned_count' => ClassSection::whereNull('lecturer_id')->count(),
        ];

        return view('teaching::admin.class-sections.index', compact(
            'classSections',
            'semesters',
            'currentSemester',
            'courses',
            'lecturers',
            'stats'
        ));
    }

    public function create(Request $request): View
    {
        $courses = Course::where('is_active', true)->orderBy('code')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $lecturers = Lecturer::with('user')->get()->sortBy('user.name');
        $selectedSemesterId = $request->input('semester_id', Semester::where('is_current', true)->value('id'));
        $selectedCourseId = $request->input('course_id');

        return view('teaching::admin.class-sections.create', compact(
            'courses',
            'semesters',
            'lecturers',
            'selectedSemesterId',
            'selectedCourseId'
        ));
    }

    public function store(StoreClassSectionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $classSection = ClassSection::create($validated);

        return redirect()
            ->route('admin.class-sections.show', $classSection)
            ->with('success', "Đã mở lớp học phần {$classSection->code} thành công.");
    }

    public function show(ClassSection $classSection): View
    {
        $classSection->load([
            'course.department',
            'semester',
            'lecturer.user',
            'schedules',
            'enrollments.student.user',
            'gradeComponents',
        ]);

        $availableLecturers = Lecturer::with('user')->get()->sortBy('user.name');

        return view('teaching::admin.class-sections.show', compact('classSection', 'availableLecturers'));
    }

    public function edit(ClassSection $classSection): View
    {
        $classSection->load(['course', 'semester', 'lecturer.user']);
        $lecturers = Lecturer::with('user')->get()->sortBy('user.name');

        return view('teaching::admin.class-sections.edit', compact('classSection', 'lecturers'));
    }

    public function update(UpdateClassSectionRequest $request, ClassSection $classSection): RedirectResponse
    {
        $validated = $request->validated();
        $classSection->update($validated);

        return redirect()
            ->route('admin.class-sections.show', $classSection)
            ->with('success', "Đã cập nhật thông tin lớp học phần {$classSection->code} thành công.");
    }

    public function assignLecturer(Request $request, ClassSection $classSection): RedirectResponse
    {
        $request->validate([
            'lecturer_id' => ['nullable', 'integer', 'exists:lecturers,id'],
        ], [
            'lecturer_id.exists' => 'Giảng viên được chọn không tồn tại trong hệ thống.',
        ]);

        $lecturerId = $request->input('lecturer_id');
        $classSection->update(['lecturer_id' => $lecturerId]);

        /** @var ClassSection $fresh */
        $fresh = $classSection->fresh(['lecturer.user']);
        $lecturer = $fresh->lecturer;
        $lecturerName = ($lecturer instanceof Lecturer && $lecturer->user) ? $lecturer->user->name : null;
        $message = $lecturerName
            ? "Đã phân công giảng viên {$lecturerName} phụ trách lớp học phần {$classSection->code}."
            : "Đã hủy gán giảng viên cho lớp học phần {$classSection->code}.";

        return back()->with('success', $message);
    }

    public function destroy(ClassSection $classSection): RedirectResponse
    {
        if ($classSection->enrolled_count > 0 || $classSection->enrollments()->exists()) {
            return back()->with('error', "Không thể xóa lớp học phần {$classSection->code} vì đã có {$classSection->enrolled_count} sinh viên đăng ký.");
        }

        $code = $classSection->code;
        $classSection->delete();

        return redirect()
            ->route('admin.class-sections.index')
            ->with('success', "Đã xóa lớp học phần {$code} thành công.");
    }
}
