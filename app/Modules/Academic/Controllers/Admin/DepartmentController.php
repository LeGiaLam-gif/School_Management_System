<?php

namespace App\Modules\Academic\Controllers\Admin;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Requests\Admin\StoreDepartmentRequest;
use App\Modules\Academic\Requests\Admin\UpdateDepartmentRequest;
use App\Modules\Identity\Models\Lecturer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Department::withCount(['programs', 'courses', 'lecturers']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $departments = $query->orderBy('code')->paginate(15)->withQueryString();

        $stats = [
            'total_departments' => Department::count(),
            'total_programs' => Program::count(),
            'total_courses' => Course::count(),
            'total_lecturers' => Lecturer::count(),
        ];

        return view('academic::admin.departments.index', compact('departments', 'stats'));
    }

    public function create(): View
    {
        return view('academic::admin.departments.create');
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('success', "Đã tạo khoa {$department->name} ({$department->code}) thành công.");
    }

    public function show(Department $department): View
    {
        $department->load([
            'programs' => fn ($q) => $q->withCount(['administrativeClasses', 'students']),
            'courses',
            'lecturers.user',
        ]);

        return view('academic::admin.departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        return view('academic::admin.departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('success', "Đã cập nhật thông tin khoa {$department->code} thành công.");
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->programs()->exists()) {
            return back()->with('error', "Không thể xóa khoa {$department->code} vì đang có ngành đào tạo trực thuộc.");
        }

        if ($department->courses()->exists()) {
            return back()->with('error', "Không thể xóa khoa {$department->code} vì đang có môn học liên kết.");
        }

        if ($department->lecturers()->exists()) {
            return back()->with('error', "Không thể xóa khoa {$department->code} vì đang có giảng viên công tác.");
        }

        $code = $department->code;
        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', "Đã xóa khoa {$code} thành công.");
    }
}
