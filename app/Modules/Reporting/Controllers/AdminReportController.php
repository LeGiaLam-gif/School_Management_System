<?php

namespace App\Modules\Reporting\Controllers;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\UserStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function index(Request $request): View
    {
        $currentSemester = Semester::where('is_current', true)->first();

        // 1. Thống kê người dùng
        $userStats = [
            'total_students' => Student::count(),
            'active_students' => Student::whereHas('user', fn ($q) => $q->where('status', UserStatus::Active->value))->count(),
            'locked_students' => Student::whereHas('user', fn ($q) => $q->where('status', UserStatus::Locked->value))->count(),
            'total_lecturers' => Lecturer::count(),
            'active_lecturers' => Lecturer::whereHas('user', fn ($q) => $q->where('status', UserStatus::Active->value))->count(),
            'locked_lecturers' => Lecturer::whereHas('user', fn ($q) => $q->where('status', UserStatus::Locked->value))->count(),
            'total_admins' => User::whereHas('roles', fn ($q) => $q->where('name', RoleName::Admin->value))->count(),
        ];

        // 2. Thống kê học vụ & cơ cấu
        $academicStats = [
            'total_departments' => Department::count(),
            'total_programs' => Program::count(),
            'total_admin_classes' => AdministrativeClass::count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_semesters' => Semester::count(),
        ];

        // 3. Thống kê lớp học phần & giảng dạy
        $teachingStats = [
            'total_sections' => ClassSection::count(),
            'current_sections' => $currentSemester ? ClassSection::where('semester_id', $currentSemester->id)->count() : 0,
            'total_enrolled' => (int) ClassSection::sum('enrolled_count'),
            'unassigned_sections' => ClassSection::whereNull('lecturer_id')->count(),
            'total_enrollments' => Enrollment::count(),
        ];

        // 4. Thống kê sinh viên theo Khoa
        $departmentsReport = Department::withCount(['programs', 'courses'])
            ->with([
                'programs' => function ($pq) {
                    $pq->withCount('administrativeClasses');
                },
            ])
            ->get();

        // 5. Danh sách các lớp học phần trong học kỳ hiện tại (hoặc kỳ mới nhất)
        $selectedSemesterId = $request->input('semester_id', $currentSemester->id ?? Semester::max('id'));
        $selectedSemester = Semester::find($selectedSemesterId);

        $sectionsInSemester = ClassSection::with(['course', 'lecturer.user'])
            ->when($selectedSemesterId, fn ($q) => $q->where('semester_id', $selectedSemesterId))
            ->orderBy('code')
            ->take(15)
            ->get();

        $allSemesters = Semester::orderBy('start_date', 'desc')->get();

        return view('reporting::admin.reports.index', compact(
            'userStats',
            'academicStats',
            'teachingStats',
            'departmentsReport',
            'sectionsInSemester',
            'currentSemester',
            'selectedSemester',
            'allSemesters'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'class_sections');
        $fileName = 'hta_report_'.$type.'_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

            if ($type === 'students') {
                fputcsv($handle, ['Mã SV', 'Họ và tên', 'Email', 'Khoa/Ngành', 'Lớp hành chính', 'Trạng thái']);
                Student::with(['user', 'administrativeClass.program.department'])
                    ->chunk(200, function ($students) use ($handle) {
                        /** @var Collection<int, Student> $students */
                        foreach ($students as $student) {
                            $adminClass = $student->administrativeClass;
                            $programName = ($adminClass && $adminClass->program) ? $adminClass->program->name : '';
                            $adminClassCode = $adminClass ? $adminClass->code : '';
                            $userStatus = $student->user->isActive() ? 'Hoạt động' : 'Đã khóa';

                            fputcsv($handle, [
                                $student->user->code ?? '',
                                $student->user->name ?? '',
                                $student->user->email ?? '',
                                $programName,
                                $adminClassCode,
                                $userStatus,
                            ]);
                        }
                    });
            } else {
                fputcsv($handle, ['Mã Lớp HP', 'Học phần', 'Học kỳ', 'Giảng viên', 'Sĩ số hiện tại', 'Sĩ số tối đa', 'Trạng thái']);
                ClassSection::with(['course', 'semester', 'lecturer.user'])
                    ->chunk(200, function ($sections) use ($handle) {
                        /** @var Collection<int, ClassSection> $sections */
                        /** @var Collection<int, ClassSection> $sections */
                        foreach ($sections as $sec) {
                            $statusVal = is_object($sec->status) ? $sec->status->value : $sec->status;
                            $lecturer = $sec->lecturer;
                            $lecturerName = ($lecturer instanceof Lecturer && $lecturer->user)
                                ? $lecturer->user->name
                                : 'Chưa phân công';

                            fputcsv($handle, [
                                $sec->code,
                                $sec->course->name ?? '',
                                $sec->semester->name ?? '',
                                $lecturerName,
                                $sec->enrolled_count,
                                $sec->capacity,
                                $statusVal,
                            ]);
                        }
                    });
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
