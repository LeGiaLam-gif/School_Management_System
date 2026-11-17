<?php

namespace App\Modules\Academic\Controllers\Admin;

use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Requests\Admin\StoreProgramRequest;
use App\Modules\Academic\Requests\Admin\UpdateProgramRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $query = Program::with('department')
            ->withCount(['administrativeClasses', 'students', 'courses']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        $programs    = $query->orderBy('code')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $stats = [
            'total_programs' => Program::count(),
            'total_classes'  => \App\Modules\Academic\Models\AdministrativeClass::count(),
            'total_students' => \App\Modules\Identity\Models\Student::count(),
            'avg_credits'    => round((float) Program::avg('total_credits_required'), 0),
        ];

        return view('academic::admin.programs.index', compact('programs', 'departments', 'stats'));
    }

    public function create(Request $request): View
    {
        $departments = Department::orderBy('name')->get();
        $selectedDeptId = $request->input('department_id');

        return view('academic::admin.programs.create', compact('departments', 'selectedDeptId'));
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $program = Program::create($request->validated());

        return redirect()
            ->route('admin.programs.show', $program)
            ->with('success', "Đã tạo ngành đào tạo {$program->name} ({$program->code}) thành công.");
    }

    public function show(Program $program): View
    {
        $program->load([
            'department',
            'administrativeClasses' => fn ($q) => $q->with(['advisorLecturer.user'])->withCount('students'),
            'students.user',
            'courses',
        ]);

        return view('academic::admin.programs.show', compact('program'));
    }

    public function edit(Program $program): View
    {
        $departments = Department::orderBy('name')->get();

        return view('academic::admin.programs.edit', compact('program', 'departments'));
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($request->validated());

        return redirect()
            ->route('admin.programs.show', $program)
            ->with('success', "Đã cập nhật thông tin ngành {$program->code} thành công.");
    }

    public function destroy(Program $program): RedirectResponse
    {
        if ($program->administrativeClasses()->exists()) {
            return back()->with('error', "Không thể xóa ngành {$program->code} vì đang có lớp hành chính thuộc ngành này.");
        }

        if ($program->students()->exists()) {
            return back()->with('error', "Không thể xóa ngành {$program->code} vì đã có sinh viên đang theo học.");
        }

        $code = $program->code;
        $program->delete();

        return redirect()
            ->route('admin.programs.index')
            ->with('success', "Đã xóa ngành đào tạo {$code} thành công.");
    }
}
