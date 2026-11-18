<?php

namespace App\Modules\Identity\Controllers\Admin;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Program;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Requests\Admin\StoreStudentRequest;
use App\Modules\Identity\Requests\Admin\UpdateStudentRequest;
use App\Modules\Identity\Services\UserService;
use App\Shared\Enums\Gender;
use App\Shared\Enums\StudentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::with(['user', 'program.department', 'administrativeClass']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($programId = $request->input('program_id')) {
            $query->where('program_id', $programId);
        }

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        if ($cohortYear = $request->input('cohort_year')) {
            $query->where('cohort_year', $cohortYear);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $students = $query->orderBy('student_code')->paginate(15)->withQueryString();

        $programs = Program::orderBy('name')->get();
        $classes  = AdministrativeClass::orderBy('name')->get();
        $cohortYears = Student::select('cohort_year')
            ->distinct()
            ->orderBy('cohort_year', 'desc')
            ->pluck('cohort_year');

        $stats = [
            'total_students'     => Student::count(),
            'studying_students'  => Student::where('status', StudentStatus::Studying->value)->count(),
            'suspended_students' => Student::where('status', StudentStatus::Suspended->value)->count(),
            'graduated_students' => Student::where('status', StudentStatus::Graduated->value)->count(),
        ];

        return view('identity::admin.students.index', compact(
            'students', 'programs', 'classes', 'cohortYears', 'stats'
        ));
    }

    public function create(Request $request): View
    {
        $programs = Program::with('department')->orderBy('name')->get();
        $classes  = AdministrativeClass::orderBy('name')->get();
        $genders  = Gender::cases();
        $statuses = StudentStatus::cases();

        $selectedClassId   = $request->input('class_id');
        $selectedProgramId = $request->input('program_id');

        return view('identity::admin.students.create', compact(
            'programs', 'classes', 'genders', 'statuses', 'selectedClassId', 'selectedProgramId'
        ));
    }

    public function store(StoreStudentRequest $request, UserService $userService): RedirectResponse
    {
        $validated = $request->validated();

        $account = [
            'code'     => $validated['student_code'],
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        $profile = [
            'student_code'  => $validated['student_code'],
            'program_id'    => $validated['program_id'],
            'class_id'      => $validated['class_id'] ?? null,
            'cohort_year'   => $validated['cohort_year'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender'        => $validated['gender'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'address'       => $validated['address'] ?? null,
            'status'        => $validated['status'] ?? StudentStatus::Studying->value,
        ];

        $user = $userService->createStudent($account, $profile);
        $student = Student::where('user_id', $user->id)->firstOrFail();

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', "Đã tạo hồ sơ và tài khoản sinh viên {$student->user->name} ({$student->student_code}) thành công.");
    }

    public function show(Student $student): View
    {
        $student->load([
            'user',
            'program.department',
            'administrativeClass.advisorLecturer.user',
            'enrollments.classSection.course',
            'academicWarnings',
        ]);

        return view('identity::admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $student->load(['user', 'program', 'administrativeClass']);

        $programs = Program::with('department')->orderBy('name')->get();
        $classes  = AdministrativeClass::orderBy('name')->get();
        $genders  = Gender::cases();
        $statuses = StudentStatus::cases();

        return view('identity::admin.students.edit', compact(
            'student', 'programs', 'classes', 'genders', 'statuses'
        ));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $student) {
            $userPayload = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'code'  => $validated['student_code'],
            ];

            if (! empty($validated['password'])) {
                $userPayload['password'] = Hash::make($validated['password']);
            }

            if (! empty($validated['user_status'])) {
                $userPayload['status'] = $validated['user_status'];
            }

            $student->user->update($userPayload);

            $student->update([
                'student_code'  => $validated['student_code'],
                'program_id'    => $validated['program_id'],
                'class_id'      => $validated['class_id'] ?? null,
                'cohort_year'   => $validated['cohort_year'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender'        => $validated['gender'] ?? null,
                'phone'         => $validated['phone'] ?? null,
                'address'       => $validated['address'] ?? null,
                'status'        => $validated['status'] ?? $student->status->value,
            ]);
        });

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', "Đã cập nhật thông tin sinh viên {$student->student_code} thành công.");
    }

    public function toggleStatus(Student $student, UserService $userService): RedirectResponse
    {
        $user = $student->user;

        if ($user->isActive()) {
            $userService->deactivate($user);
            $msg = "Đã khóa tài khoản đăng nhập của sinh viên {$student->student_code}.";
        } else {
            $userService->activate($user);
            $msg = "Đã kích hoạt lại tài khoản đăng nhập của sinh viên {$student->student_code}.";
        }

        return back()->with('success', $msg);
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->enrollments()->exists()) {
            return back()->with('error', "Không thể xóa sinh viên {$student->student_code} vì đã có lịch sử đăng ký học phần. Vui lòng chuyển trạng thái thôi học hoặc khóa tài khoản thay vì xóa.");
        }

        if ($student->academicWarnings()->exists()) {
            return back()->with('error', "Không thể xóa sinh viên {$student->student_code} vì có bản ghi cảnh báo học vụ.");
        }

        $name = $student->user->name ?? $student->student_code;
        $user = $student->user;

        DB::transaction(function () use ($student, $user) {
            $student->delete();
            $user?->delete();
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', "Đã xóa sinh viên {$name} thành công.");
    }
}

