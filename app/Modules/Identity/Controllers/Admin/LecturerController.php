<?php

namespace App\Modules\Identity\Controllers\Admin;

use App\Modules\Academic\Models\Department;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Requests\Admin\StoreLecturerRequest;
use App\Modules\Identity\Requests\Admin\UpdateLecturerRequest;
use App\Modules\Identity\Services\UserService;
use App\Shared\Enums\UserStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LecturerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lecturer::with(['user', 'department'])
            ->withCount(['classSections', 'advisedClasses']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('lecturer_code', 'like', "%{$search}%")
                    ->orWhere('academic_title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($status = $request->input('status')) {
            $query->whereHas('user', function ($uq) use ($status) {
                $uq->where('status', $status);
            });
        }

        $lecturers = $query->orderBy('lecturer_code')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $stats = [
            'total_lecturers' => Lecturer::count(),
            'active_lecturers' => Lecturer::whereHas('user', fn ($q) => $q->where('status', UserStatus::Active->value))->count(),
            'locked_lecturers' => Lecturer::whereHas('user', fn ($q) => $q->where('status', UserStatus::Locked->value))->count(),
            'total_departments' => Department::count(),
        ];

        return view('identity::admin.lecturers.index', compact('lecturers', 'departments', 'stats'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('identity::admin.lecturers.create', compact('departments'));
    }

    public function store(StoreLecturerRequest $request, UserService $userService): RedirectResponse
    {
        $validated = $request->validated();

        $account = [
            'code'     => $validated['lecturer_code'],
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        $profile = [
            'lecturer_code'  => $validated['lecturer_code'],
            'department_id'  => $validated['department_id'],
            'academic_title' => $validated['academic_title'] ?? null,
            'phone'          => $validated['phone'] ?? null,
            'office'         => $validated['office'] ?? null,
            'address'        => $validated['address'] ?? null,
        ];

        $user = $userService->createLecturer($account, $profile);
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();

        return redirect()
            ->route('admin.lecturers.show', $lecturer)
            ->with('success', "Đã tạo hồ sơ và tài khoản giảng viên {$lecturer->user->name} ({$lecturer->lecturer_code}) thành công.");
    }

    public function show(Lecturer $lecturer): View
    {
        $lecturer->load([
            'user',
            'department',
            'advisedClasses.program',
            'classSections.course',
        ]);

        return view('identity::admin.lecturers.show', compact('lecturer'));
    }

    public function edit(Lecturer $lecturer): View
    {
        $lecturer->load(['user', 'department']);
        $departments = Department::orderBy('name')->get();

        return view('identity::admin.lecturers.edit', compact('lecturer', 'departments'));
    }

    public function update(UpdateLecturerRequest $request, Lecturer $lecturer): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $lecturer) {
            $userPayload = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'code'  => $validated['lecturer_code'],
            ];

            if (! empty($validated['password'])) {
                $userPayload['password'] = Hash::make($validated['password']);
            }

            if (! empty($validated['status'])) {
                $userPayload['status'] = $validated['status'];
            }

            $lecturer->user->update($userPayload);

            $lecturer->update([
                'lecturer_code'  => $validated['lecturer_code'],
                'department_id'  => $validated['department_id'],
                'academic_title' => $validated['academic_title'] ?? null,
                'phone'          => $validated['phone'] ?? null,
                'office'         => $validated['office'] ?? null,
                'address'        => $validated['address'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.lecturers.show', $lecturer)
            ->with('success', "Đã cập nhật thông tin giảng viên {$lecturer->lecturer_code} thành công.");
    }

    public function toggleStatus(Lecturer $lecturer, UserService $userService): RedirectResponse
    {
        $user = $lecturer->user;

        if ($user->isActive()) {
            $userService->deactivate($user);
            $msg = "Đã khóa tài khoản giảng viên {$lecturer->lecturer_code}.";
        } else {
            $userService->activate($user);
            $msg = "Đã kích hoạt lại tài khoản giảng viên {$lecturer->lecturer_code}.";
        }

        return back()->with('success', $msg);
    }

    public function destroy(Lecturer $lecturer): RedirectResponse
    {
        if ($lecturer->advisedClasses()->exists()) {
            return back()->with('error', "Không thể xóa giảng viên {$lecturer->lecturer_code} vì đang cố vấn cho {$lecturer->advisedClasses()->count()} lớp hành chính. Vui lòng chuyển quyền cố vấn hoặc khóa tài khoản thay vì xóa.");
        }

        if ($lecturer->classSections()->exists()) {
            return back()->with('error', "Không thể xóa giảng viên {$lecturer->lecturer_code} vì đang phụ trách {$lecturer->classSections()->count()} lớp học phần. Vui lòng khóa tài khoản thay vì xóa.");
        }

        $name = $lecturer->user->name ?? $lecturer->lecturer_code;
        $user = $lecturer->user;

        DB::transaction(function () use ($lecturer, $user) {
            $lecturer->delete();
            $user?->delete();
        });

        return redirect()
            ->route('admin.lecturers.index')
            ->with('success', "Đã xóa giảng viên {$name} thành công.");
    }
}
