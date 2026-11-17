<?php

namespace App\Modules\Academic\Controllers\Admin;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Requests\Admin\StoreAdministrativeClassRequest;
use App\Modules\Academic\Requests\Admin\UpdateAdministrativeClassRequest;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class AdministrativeClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = AdministrativeClass::with(['program.department', 'advisorLecturer.user'])
            ->withCount('students');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($progId = $request->input('program_id')) {
            $query->where('program_id', $progId);
        }

        if ($academicYear = $request->input('academic_year')) {
            $query->where('academic_year', $academicYear);
        }

        $classes = $query->orderBy('academic_year', 'desc')
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        $programs = Program::orderBy('name')->get();
        $years = AdministrativeClass::select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        $stats = [
            'total_classes' => AdministrativeClass::count(),
            'total_students' => Student::count(),
            'with_advisor' => AdministrativeClass::whereNotNull('advisor_lecturer_id')->count(),
            'total_programs' => Program::count(),
        ];

        return view('academic::admin.administrative-classes.index', compact('classes', 'programs', 'years', 'stats'));
    }

    public function create(Request $request): View
    {
        $programs = Program::with('department')->orderBy('name')->get();
        $lecturers = Lecturer::with(['user', 'department'])->get();
        $selectedProgramId = $request->input('program_id');

        return view('academic::admin.administrative-classes.create', compact('programs', 'lecturers', 'selectedProgramId'));
    }

    public function store(StoreAdministrativeClassRequest $request): RedirectResponse
    {
        $class = AdministrativeClass::create($request->validated());

        return redirect()
            ->route('admin.administrative-classes.show', $class)
            ->with('success', "Đã tạo lớp hành chính {$class->name} ({$class->code}) thành công.");
    }

    public function show(AdministrativeClass $administrativeClass): View
    {
        $administrativeClass->load([
            'program.department',
            'advisorLecturer.user',
            'advisorLecturer.department',
            'students.user',
        ]);

        return view('academic::admin.administrative-classes.show', compact('administrativeClass'));
    }

    public function edit(AdministrativeClass $administrativeClass): View
    {
        $programs = Program::with('department')->orderBy('name')->get();
        $lecturers = Lecturer::with(['user', 'department'])->get();

        return view('academic::admin.administrative-classes.edit', compact('administrativeClass', 'programs', 'lecturers'));
    }

    public function update(UpdateAdministrativeClassRequest $request, AdministrativeClass $administrativeClass): RedirectResponse
    {
        $administrativeClass->update($request->validated());

        return redirect()
            ->route('admin.administrative-classes.show', $administrativeClass)
            ->with('success', "Đã cập nhật thông tin lớp {$administrativeClass->code} thành công.");
    }

    public function destroy(AdministrativeClass $administrativeClass): RedirectResponse
    {
        if ($administrativeClass->students()->exists()) {
            return back()->with('error', "Không thể xóa lớp {$administrativeClass->code} vì đang có {$administrativeClass->students()->count()} sinh viên thuộc lớp này.");
        }

        $code = $administrativeClass->code;
        $administrativeClass->delete();

        return redirect()
            ->route('admin.administrative-classes.index')
            ->with('success', "Đã xóa lớp hành chính {$code} thành công.");
    }
}
