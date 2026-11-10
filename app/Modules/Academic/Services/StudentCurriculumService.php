<?php

namespace App\Modules\Academic\Services;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Program;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\CourseType;
use Illuminate\Support\Collection;
use Throwable;

/**
 * @phpstan-type CourseListItem object{
 *   code: string,
 *   name: string,
 *   credits: int,
 *   theory_periods: int,
 *   practice_periods: int,
 *   department_name: string,
 *   department_code: string,
 *   description: string,
 *   prerequisites: array<int, string>
 * }&\stdClass
 * @phpstan-type DatabaseCourseListItem object{
 *   id: int,
 *   code: string,
 *   name: string,
 *   credits: int,
 *   theory_periods: int,
 *   practice_periods: int,
 *   department_name: string,
 *   department_code: string,
 *   description: string,
 *   prerequisites: array<int, string>,
 *   prerequisite_names: array<int, non-falsy-string>
 * }&\stdClass
 * @phpstan-type CurriculumCourseItem object{
 *   code: string,
 *   name: string,
 *   credits: int,
 *   theory_periods: int,
 *   practice_periods: int,
 *   is_compulsory: bool,
 *   type_label: string,
 *   type_badge: string,
 *   prerequisites: array<int, string>
 * }
 * @phpstan-type CurriculumSemester array{
 *   semester_no: int,
 *   semester_title: string,
 *   total_credits: int,
 *   courses: list<CurriculumCourseItem>
 * }
 * @phpstan-type CurriculumData object{
 *   program_name: string,
 *   program_code: string,
 *   department_name: string,
 *   duration_years: int,
 *   total_credits: int,
 *   required_credits: int,
 *   elective_credits: int,
 *   semesters: list<CurriculumSemester>
 * }
 */
class StudentCurriculumService
{
    /**
     * Lay toan bo khung chuong trinh dao tao theo nganh cua sinh vien.
     * Uu tien doc DB neu co san, fallback sang Mock Dataset day du.
     *
     * @return CurriculumData
     */
    public function getCurriculum(?User $user): object
    {
        if ($user !== null) {
            try {
                $student = $user->student()->first();
                if ($student instanceof Student && $student->program_id !== null) {
                    $program = Program::with(['department', 'courses' => function ($q) {
                        $q->with('prerequisites');
                    }])->find($student->program_id);

                    if ($program && $program->courses->isNotEmpty()) {
                        return $this->formatCurriculumFromDb($program);
                    }
                }
            } catch (Throwable) {
                // Database chua co table hoac chua seed -> fallback sang Mock Data
            }
        }

        return $this->getMockCurriculum();
    }

    /**
     * Tra cuu danh muc mon hoc toan truong (co tim kiem va loc).
     *
     * @param  array{keyword?: ?string, department?: ?string, credits?: ?int, type?: ?string}  $filters
     * @return Collection<int, DatabaseCourseListItem>|Collection<int, CourseListItem>
     */
    public function getCourses(array $filters = []): Collection
    {
        try {
            $query = Course::with(['department', 'prerequisites'])->where('is_active', true);

            if (! empty($filters['keyword'])) {
                $kw = '%'.$filters['keyword'].'%';
                $query->where(function ($q) use ($kw) {
                    $q->where('code', 'like', $kw)->orWhere('name', 'like', $kw);
                });
            }

            if (! empty($filters['department'])) {
                $query->whereHas('department', function ($q) use ($filters) {
                    $q->where('code', $filters['department'])->orWhere('name', 'like', '%'.$filters['department'].'%');
                });
            }

            if (! empty($filters['credits'])) {
                $query->where('credits', (int) $filters['credits']);
            }

            $courses = $query->get();

            if ($courses->isNotEmpty()) {
                $mappedCourses = $courses->map(function (Course $c) {
                    $department = $c->department;

                    return (object) [
                        'id' => $c->id,
                        'code' => $c->code,
                        'name' => $c->name,
                        'credits' => $c->credits,
                        'theory_periods' => $c->theory_periods,
                        'practice_periods' => $c->practice_periods,
                        'department_name' => $department !== null ? $department->name : 'Khoa Đào tạo',
                        'department_code' => $department !== null ? $department->code : '',
                        'description' => $c->description ?? 'Không có mô tả chi tiết.',
                        'prerequisites' => $c->prerequisites
                            ->map(fn (Course $prerequisite): string => $prerequisite->code)
                            ->values()
                            ->all(),
                        'prerequisite_names' => $c->prerequisites->map(function (Course $p) {
                            return $p->code.' - '.$p->name;
                        })->all(),
                    ];
                });

                return $mappedCourses;
            }
        } catch (Throwable) {
            // DB chua san sang -> tiep tuc xuong Mock Data
        }

        return $this->getMockCourses($filters);
    }

    /**
     * Chi tiet hoc phan va cac hoc phan tien quyet / duoc yeu cau.
     */
    public function getCourseDetail(string $code): ?object
    {
        try {
            $course = Course::with(['department', 'prerequisites', 'requiredBy'])
                ->where('code', $code)
                ->first();

            if ($course) {
                return (object) [
                    'code' => $course->code,
                    'name' => $course->name,
                    'credits' => $course->credits,
                    'theory_periods' => $course->theory_periods,
                    'practice_periods' => $course->practice_periods,
                    'department_name' => $course->department->name,
                    'description' => $course->description ?? 'Môn học cung cấp kiến thức nền tảng và nâng cao phục vụ chuyên ngành.',
                    'prerequisites' => $course->prerequisites->map(fn ($p) => (object) ['code' => $p->code, 'name' => $p->name, 'credits' => $p->credits])->all(),
                    'required_by' => $course->requiredBy->map(fn ($r) => (object) ['code' => $r->code, 'name' => $r->name, 'credits' => $r->credits])->all(),
                ];
            }
        } catch (Throwable) {
            // Fallback sang Mock Data
        }

        return $this->getMockCourseDetail($code);
    }

    /**
     * Format du lieu that tu DB theo cac hoc ky goi y.
     *
     * @return CurriculumData
     */
    private function formatCurriculumFromDb(Program $program): object
    {
        /** @var array<int, CurriculumSemester> $grouped */
        $grouped = [];
        $totalCredits = 0;
        $requiredCredits = 0;
        $electiveCredits = 0;

        for ($sem = 1; $sem <= ($program->duration_years * 2); $sem++) {
            $grouped[$sem] = [
                'semester_no' => $sem,
                'semester_title' => "Học kỳ {$sem}",
                'total_credits' => 0,
                'courses' => [],
            ];
        }

        foreach ($program->courses as $course) {
            $semNo = $course->pivot->suggested_semester_no ?? 1;
            $type = $course->pivot->type ?? 'required';
            $isCompulsory = ($type === 'required' || $type === CourseType::Required->value);

            $courseObj = (object) [
                'code' => $course->code,
                'name' => $course->name,
                'credits' => $course->credits,
                'theory_periods' => $course->theory_periods,
                'practice_periods' => $course->practice_periods,
                'is_compulsory' => $isCompulsory,
                'type_label' => $isCompulsory ? 'Bắt buộc' : 'Tự chọn',
                'type_badge' => $isCompulsory ? 'bg-primary' : 'bg-warning text-dark',
                'prerequisites' => $course->prerequisites->pluck('code')->all(),
            ];

            if (isset($grouped[$semNo])) {
                $grouped[$semNo]['courses'][] = $courseObj;
                $grouped[$semNo]['total_credits'] += $course->credits;
            }

            $totalCredits += $course->credits;
            if ($isCompulsory) {
                $requiredCredits += $course->credits;
            } else {
                $electiveCredits += $course->credits;
            }
        }

        $department = $program->department;

        return (object) [
            'program_name' => $program->name,
            'program_code' => $program->code,
            'department_name' => $department !== null ? $department->name : 'Khoa Đào tạo',
            'duration_years' => $program->duration_years,
            'total_credits' => $program->total_credits_required ?: $totalCredits,
            'required_credits' => $requiredCredits,
            'elective_credits' => $electiveCredits,
            'semesters' => array_values($grouped),
        ];
    }

    /**
     * Mock Khung chuong trinh dao tao chuan nganh Ky thuat Phan mem.
     *
     * @return CurriculumData
     */
    private function getMockCurriculum(): object
    {
        $semesters = [
            [
                'semester_no' => 1,
                'semester_title' => 'Học kỳ 1 (Năm 1 - Học kỳ Thu)',
                'total_credits' => 18,
                'courses' => [
                    (object) ['code' => 'CS101', 'name' => 'Nhập môn lập trình', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                    (object) ['code' => 'MATH101', 'name' => 'Giải tích 1', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                    (object) ['code' => 'MATH102', 'name' => 'Đại số tuyến tính', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                    (object) ['code' => 'ENG101', 'name' => 'Tiếng Anh học thuật 1', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                    (object) ['code' => 'PHY101', 'name' => 'Vật lý đại cương', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                    (object) ['code' => 'GEN101', 'name' => 'Triết học Mác - Lênin', 'credits' => 2, 'theory_periods' => 30, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                ],
            ],
            [
                'semester_no' => 2,
                'semester_title' => 'Học kỳ 2 (Năm 1 - Học kỳ Xuân)',
                'total_credits' => 19,
                'courses' => [
                    (object) ['code' => 'CS102', 'name' => 'Kỹ thuật lập trình', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS101']],
                    (object) ['code' => 'MATH103', 'name' => 'Giải tích 2', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['MATH101']],
                    (object) ['code' => 'CS103', 'name' => 'Cấu trúc rời rạc', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                    (object) ['code' => 'ENG102', 'name' => 'Tiếng Anh học thuật 2', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['ENG101']],
                    (object) ['code' => 'CS104', 'name' => 'Kiến trúc máy tính', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => []],
                    (object) ['code' => 'GEN102', 'name' => 'Kinh tế chính trị Mác - Lênin', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['GEN101']],
                ],
            ],
            [
                'semester_no' => 3,
                'semester_title' => 'Học kỳ 3 (Năm 2 - Học kỳ Thu)',
                'total_credits' => 18,
                'courses' => [
                    (object) ['code' => 'CS201', 'name' => 'Cấu trúc dữ liệu và giải thuật', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS102']],
                    (object) ['code' => 'CS202', 'name' => 'Cơ sở dữ liệu', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS102']],
                    (object) ['code' => 'CS203', 'name' => 'Hệ điều hành', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS104']],
                    (object) ['code' => 'SE201', 'name' => 'Nhập môn Công nghệ phần mềm', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS102']],
                    (object) ['code' => 'MATH201', 'name' => 'Xác suất thống kê', 'credits' => 4, 'theory_periods' => 60, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['MATH101']],
                ],
            ],
            [
                'semester_no' => 4,
                'semester_title' => 'Học kỳ 4 (Năm 2 - Học kỳ Xuân)',
                'total_credits' => 18,
                'courses' => [
                    (object) ['code' => 'CS204', 'name' => 'Mạng máy tính', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS203']],
                    (object) ['code' => 'SE202', 'name' => 'Thiết kế kiến trúc phần mềm', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['SE201']],
                    (object) ['code' => 'SE203', 'name' => 'Lập trình hướng đối tượng nâng cao', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS201']],
                    (object) ['code' => 'SE204', 'name' => 'Hệ quản trị cơ sở dữ liệu', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS202']],
                    (object) ['code' => 'GEN103', 'name' => 'Chủ nghĩa xã hội khoa học', 'credits' => 2, 'theory_periods' => 30, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['GEN102']],
                    (object) ['code' => 'SE205', 'name' => 'Lập trình Web căn bản', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['CS102']],
                ],
            ],
            [
                'semester_no' => 5,
                'semester_title' => 'Học kỳ 5 (Năm 3 - Học kỳ Thu)',
                'total_credits' => 18,
                'courses' => [
                    (object) ['code' => 'SE301', 'name' => 'Quản lý dự án phần mềm', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['SE201']],
                    (object) ['code' => 'SE302', 'name' => 'Kiểm thử & Đảm bảo chất lượng PM', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['SE202']],
                    (object) ['code' => 'SE303', 'name' => 'Lập trình ứng dụng di động', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['SE203']],
                    (object) ['code' => 'SE304', 'name' => 'Điện toán đám mây & DevOps', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['CS204']],
                    (object) ['code' => 'SE305', 'name' => 'Trí tuệ nhân tạo nhập môn', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['CS201']],
                    (object) ['code' => 'GEN104', 'name' => 'Lịch sử Đảng Cộng sản Việt Nam', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['GEN103']],
                ],
            ],
            [
                'semester_no' => 6,
                'semester_title' => 'Học kỳ 6 (Năm 3 - Học kỳ Xuân)',
                'total_credits' => 17,
                'courses' => [
                    (object) ['code' => 'SE306', 'name' => 'An toàn và bảo mật thông tin', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['CS204']],
                    (object) ['code' => 'SE307', 'name' => 'Phát triển phần mềm hướng dịch vụ (SOA)', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['SE202']],
                    (object) ['code' => 'SE308', 'name' => 'Xử lý dữ liệu lớn (Big Data)', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['CS202']],
                    (object) ['code' => 'SE309', 'name' => 'Đồ án chuyên ngành Kỹ thuật phần mềm', 'credits' => 4, 'theory_periods' => 0, 'practice_periods' => 60, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['SE302']],
                    (object) ['code' => 'GEN105', 'name' => 'Tư tưởng Hồ Chí Minh', 'credits' => 2, 'theory_periods' => 30, 'practice_periods' => 0, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['GEN104']],
                    (object) ['code' => 'BUS101', 'name' => 'Kỹ năng nghề nghiệp & Khởi nghiệp', 'credits' => 2, 'theory_periods' => 30, 'practice_periods' => 0, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => []],
                ],
            ],
            [
                'semester_no' => 7,
                'semester_title' => 'Học kỳ 7 (Năm 4 - Học kỳ Thu)',
                'total_credits' => 16,
                'courses' => [
                    (object) ['code' => 'SE401', 'name' => 'Thực tập tốt nghiệp tại doanh nghiệp', 'credits' => 8, 'theory_periods' => 0, 'practice_periods' => 120, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['SE309']],
                    (object) ['code' => 'SE402', 'name' => 'Chuyên đề Công nghệ phần mềm tiên tiến', 'credits' => 4, 'theory_periods' => 45, 'practice_periods' => 15, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['SE309']],
                    (object) ['code' => 'SE403', 'name' => 'Quản trị hệ thống và dịch vụ mạng', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['CS204']],
                ],
            ],
            [
                'semester_no' => 8,
                'semester_title' => 'Học kỳ 8 (Năm 4 - Học kỳ Xuân)',
                'total_credits' => 16,
                'courses' => [
                    (object) ['code' => 'SE404', 'name' => 'Khóa luận tốt nghiệp Kỹ sư phần mềm', 'credits' => 10, 'theory_periods' => 0, 'practice_periods' => 150, 'is_compulsory' => true, 'type_label' => 'Bắt buộc', 'type_badge' => 'bg-primary', 'prerequisites' => ['SE401']],
                    (object) ['code' => 'SE405', 'name' => 'Các công nghệ phần mềm mới (Blockchain/AI)', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => ['SE307']],
                    (object) ['code' => 'SE406', 'name' => 'Pháp luật trong Công nghệ thông tin', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'is_compulsory' => false, 'type_label' => 'Tự chọn', 'type_badge' => 'bg-warning text-dark', 'prerequisites' => []],
                ],
            ],
        ];

        return (object) [
            'program_name' => 'Kỹ thuật Phần mềm',
            'program_code' => 'SE01',
            'department_name' => 'Công nghệ Thông tin',
            'duration_years' => 4,
            'total_credits' => 140,
            'required_credits' => 114,
            'elective_credits' => 26,
            'semesters' => $semesters,
        ];
    }

    /**
     * Mock danh muc mon hoc toan truong co bo loc tim kiem.
     *
     * @param  array{keyword?: ?string, department?: ?string, credits?: ?int, type?: ?string}  $filters
     * @return Collection<int, CourseListItem>
     */
    private function getMockCourses(array $filters): Collection
    {
        /** @var Collection<int, CourseListItem> $all */
        $all = collect([
            (object) ['code' => 'CS101', 'name' => 'Nhập môn lập trình', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Cung cấp cú pháp C/C++, biến, vòng lặp, mảng, con trỏ và hàm căn bản.', 'prerequisites' => []],
            (object) ['code' => 'CS102', 'name' => 'Kỹ thuật lập trình', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Đệ quy, cấp phát động, xử lý tập tin, đệ quy quay lui và kỹ thuật giải bài toán.', 'prerequisites' => ['CS101']],
            (object) ['code' => 'CS201', 'name' => 'Cấu trúc dữ liệu và giải thuật', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Danh sách liên kết, Stack, Queue, Cây nhị phân, Cây AVL, Đồ thị và thuật toán sắp xếp.', 'prerequisites' => ['CS102']],
            (object) ['code' => 'CS202', 'name' => 'Cơ sở dữ liệu', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Mô hình dữ liệu quan hệ, đại số quan hệ, SQL, chuẩn hóa 1NF, 2NF, 3NF, BCNF.', 'prerequisites' => ['CS102']],
            (object) ['code' => 'CS203', 'name' => 'Hệ điều hành', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Quản lý tiến trình, luồng, lập lịch CPU, đồng bộ tiến trình, Semaphore, Deadlock và quản lý bộ nhớ.', 'prerequisites' => ['CS104']],
            (object) ['code' => 'CS204', 'name' => 'Mạng máy tính', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'department_name' => 'Khoa Mạng & Truyền thông', 'department_code' => 'MMT', 'description' => 'Mô hình OSI và TCP/IP, IP addressing, Subnetting, Routing protocols, TCP/UDP, DNS, HTTP.', 'prerequisites' => ['CS203']],
            (object) ['code' => 'SE201', 'name' => 'Nhập môn Công nghệ phần mềm', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Quy trình phát triển phần mềm Agile/Scrum, phân tích yêu cầu, UML, mô hình hóa Use Case.', 'prerequisites' => ['CS102']],
            (object) ['code' => 'SE202', 'name' => 'Thiết kế kiến trúc phần mềm', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Design Patterns GoF, kiến trúc MVC, Microservices, Clean Architecture, Domain-Driven Design.', 'prerequisites' => ['SE201']],
            (object) ['code' => 'SE203', 'name' => 'Lập trình hướng đối tượng nâng cao', 'credits' => 4, 'theory_periods' => 30, 'practice_periods' => 30, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Kế thừa, đa hình, Interface, Generic programming, Design Patterns thực chiến trên Java / C#.', 'prerequisites' => ['CS201']],
            (object) ['code' => 'SE205', 'name' => 'Lập trình Web căn bản', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'HTML5, CSS3, JavaScript, RESTful API, Single Page Applications, backend Node.js / PHP.', 'prerequisites' => ['CS102']],
            (object) ['code' => 'SE301', 'name' => 'Quản lý dự án phần mềm', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Ước lượng chi phí, quản trị rủi ro, quản lý phạm vi, công cụ Jira/GitLab, lập kế hoạch Sprint.', 'prerequisites' => ['SE201']],
            (object) ['code' => 'SE302', 'name' => 'Kiểm thử & Đảm bảo chất lượng PM', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'department_name' => 'Khoa Công nghệ Thông tin', 'department_code' => 'CNTT', 'description' => 'Kiểm thử hộp trắng, kiểm thử hộp đen, Unit Test, Integration Test, Automation Testing với Selenium.', 'prerequisites' => ['SE202']],
            (object) ['code' => 'SE304', 'name' => 'Điện toán đám mây & DevOps', 'credits' => 3, 'theory_periods' => 30, 'practice_periods' => 15, 'department_name' => 'Khoa Mạng & Truyền thông', 'department_code' => 'MMT', 'description' => 'Ảo hóa, Container Docker, Kubernetes, CI/CD pipeline, AWS Cloud cơ bản, IaC với Terraform.', 'prerequisites' => ['CS204']],
            (object) ['code' => 'SE305', 'name' => 'Trí tuệ nhân tạo nhập môn', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'department_name' => 'Khoa Khoa học Dữ liệu', 'department_code' => 'KHDL', 'description' => 'Tìm kiếm theo heuristic (A*), thuật toán di truyền, Machine Learning căn bản (Hồi quy, Phân loại).', 'prerequisites' => ['CS201']],
            (object) ['code' => 'MATH101', 'name' => 'Giải tích 1', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'department_name' => 'Khoa Toán - Tin', 'department_code' => 'TOAN', 'description' => 'Giới hạn, đạo hàm, vi phân hàm một biến, tích phân Riemann và các ứng dụng thực tế.', 'prerequisites' => []],
            (object) ['code' => 'MATH102', 'name' => 'Đại số tuyến tính', 'credits' => 3, 'theory_periods' => 45, 'practice_periods' => 0, 'department_name' => 'Khoa Toán - Tin', 'department_code' => 'TOAN', 'description' => 'Ma trận, định thức, không gian vector, phép biến đổi tuyến tính, giá trị riêng và vector riêng.', 'prerequisites' => []],
        ]);

        if (! empty($filters['keyword'])) {
            $kw = mb_strtolower(trim($filters['keyword']));
            $all = $all->filter(function ($item) use ($kw) {
                return str_contains(mb_strtolower($item->code), $kw)
                    || str_contains(mb_strtolower($item->name), $kw)
                    || str_contains(mb_strtolower($item->department_name), $kw);
            });
        }

        if (! empty($filters['department'])) {
            $dept = mb_strtolower(trim($filters['department']));
            $all = $all->filter(function ($item) use ($dept) {
                return str_contains(mb_strtolower($item->department_code), $dept)
                    || str_contains(mb_strtolower($item->department_name), $dept);
            });
        }

        if (! empty($filters['credits'])) {
            $cr = (int) $filters['credits'];
            $all = $all->filter(fn ($item) => (int) $item->credits === $cr);
        }

        return $all->values();
    }

    /**
     * Mock chi tiet mon hoc.
     */
    private function getMockCourseDetail(string $code): ?object
    {
        $courses = $this->getMockCourses([]);
        $found = $courses->firstWhere('code', strtoupper($code));

        if (! $found) {
            return null;
        }

        // Tim cac mon coi mon nay la tien quyet
        $requiredBy = $courses->filter(function ($c) use ($found) {
            return in_array($found->code, $c->prerequisites ?? [], true);
        })->map(fn ($r) => (object) [
            'code' => $r->code,
            'name' => $r->name,
            'credits' => $r->credits,
        ])->values()->all();

        // Tim thong tin mon tien quyet
        $prereqList = collect($found->prerequisites ?? [])->map(function ($pCode) use ($courses) {
            $p = $courses->firstWhere('code', $pCode);

            return (object) [
                'code' => $pCode,
                'name' => $p->name ?? "Học phần {$pCode}",
                'credits' => $p->credits ?? 3,
            ];
        })->all();

        return (object) [
            'code' => $found->code,
            'name' => $found->name,
            'credits' => $found->credits,
            'theory_periods' => $found->theory_periods,
            'practice_periods' => $found->practice_periods,
            'department_name' => $found->department_name,
            'description' => $found->description,
            'prerequisites' => $prereqList,
            'required_by' => $requiredBy,
        ];
    }
}
