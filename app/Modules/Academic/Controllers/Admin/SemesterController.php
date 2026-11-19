<?php

namespace App\Modules\Academic\Controllers\Admin;

use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Requests\Admin\StoreSemesterRequest;
use App\Modules\Academic\Requests\Admin\UpdateSemesterRequest;
use App\Modules\Academic\Services\SemesterService;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function index(Request $request): View
    {
        $query = Semester::withCount('classSections');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('academic_year', 'like', "%{$search}%");
            });
        }

        if ($academicYear = $request->input('academic_year')) {
            $query->where('academic_year', $academicYear);
        }

        if ($request->has('is_current') && $request->input('is_current') !== '') {
            $query->where('is_current', (bool) $request->input('is_current'));
        }

        $semesters = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();

        $academicYears = Semester::select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        $currentSemester = Semester::where('is_current', true)->first();

        $stats = [
            'total_semesters' => Semester::count(),
            'current_name'    => $currentSemester ? ($currentSemester->name . ' (' . $currentSemester->academic_year . ')') : 'Chưa thiết lập',
            'open_reg_count'  => Semester::all()->filter(fn ($s) => $s->isRegistrationOpen())->count(),
            'total_sections'  => ClassSection::count(),
        ];

        return view('academic::admin.semesters.index', compact('semesters', 'academicYears', 'stats', 'currentSemester'));
    }

    public function create(): View
    {
        return view('academic::admin.semesters.create');
    }

    public function store(StoreSemesterRequest $request, SemesterService $semesterService): RedirectResponse
    {
        $validated = $request->validated();
        $isCurrent = $validated['is_current'] ?? false;
        unset($validated['is_current']);

        $semester = Semester::create($validated);

        if ($isCurrent) {
            $semesterService->setCurrent($semester);
        }

        return redirect()
            ->route('admin.semesters.show', $semester)
            ->with('success', "Đã tạo học kỳ {$semester->name} ({$semester->code}) thành công.");
    }

    public function show(Semester $semester): View
    {
        $semester->load([
            'classSections.course.department',
            'classSections.lecturer.user',
        ]);

        return view('academic::admin.semesters.show', compact('semester'));
    }

    public function edit(Semester $semester): View
    {
        return view('academic::admin.semesters.edit', compact('semester'));
    }

    public function update(UpdateSemesterRequest $request, Semester $semester, SemesterService $semesterService): RedirectResponse
    {
        $validated = $request->validated();
        $isCurrent = $validated['is_current'] ?? false;
        unset($validated['is_current']);

        $semester->update($validated);

        if ($isCurrent) {
            $semesterService->setCurrent($semester);
        }

        return redirect()
            ->route('admin.semesters.show', $semester)
            ->with('success', "Đã cập nhật học kỳ {$semester->code} thành công.");
    }

    public function setCurrent(Semester $semester, SemesterService $semesterService): RedirectResponse
    {
        $semesterService->setCurrent($semester);

        return back()->with('success', "Đã thiết lập {$semester->name} ({$semester->academic_year}) làm học kỳ hiện tại của hệ thống.");
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        if ($semester->is_current) {
            return back()->with('error', "Không thể xóa học kỳ {$semester->code} vì đang được đặt làm học kỳ hiện tại.");
        }

        if ($semester->classSections()->exists()) {
            return back()->with('error', "Không thể xóa học kỳ {$semester->code} vì đã có lớp học phần được mở trong kỳ.");
        }

        $code = $semester->code;
        $semester->delete();

        return redirect()
            ->route('admin.semesters.index')
            ->with('success', "Đã xóa học kỳ {$code} thành công.");
    }
}
