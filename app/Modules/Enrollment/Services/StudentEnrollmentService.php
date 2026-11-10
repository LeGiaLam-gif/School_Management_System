<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Collection;
use Throwable;

class StudentEnrollmentService
{
    public function __construct(
        private readonly ?EnrollmentService $enrollmentService = null
    ) {}

    /**
     * Lay toan bo du lieu dang ky hoc phan cua sinh vien trong hoc ky hien tai.
     */
    public function getEnrollmentData(?User $user): object
    {
        // 1. Thu lay du lieu tu Database neu co
        if ($user !== null) {
            try {
                $student = $user->student;
                if ($student) {
                    $dbEnrollments = Enrollment::with([
                        'classSection.course',
                        'classSection.lecturer.user',
                        'classSection.schedules.room',
                        'classSection.semester',
                    ])
                        ->where('student_id', $student->id)
                        ->where('status', EnrollmentStatus::Registered)
                        ->get();

                    if ($dbEnrollments->isNotEmpty()) {
                        return $this->formatFromDb($user, $dbEnrollments);
                    }
                }
            } catch (Throwable) {
                // Database chua san sang -> dung Mock Data
            }
        }

        // 2. Mock Data Fallback
        return $this->getMockEnrollmentData();
    }

    /**
     * Thuc hien dang ky hoc phan.
     *
     * @return array{success: bool, message: string}
     */
    public function register(?User $user, int|string $sectionId): array
    {
        // Thu dang ky qua Eloquent DB neu co
        if ($user !== null && is_numeric($sectionId) && $this->enrollmentService !== null) {
            try {
                $student = $user->student;
                if ($student) {
                    $this->enrollmentService->register($student, (int) $sectionId);

                    return [
                        'success' => true,
                        'message' => 'Đăng ký lớp học phần thành công qua hệ thống.',
                    ];
                }
            } catch (BusinessRuleException $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            } catch (Throwable) {
                // DB chua co ban ghi/chua setup -> chuyen sang mock
            }
        }

        // Xu ly tren Session Mock
        return $this->registerMockSection((string) $sectionId);
    }

    /**
     * Thuc hien huy dang ky hoc phan (Drop).
     *
     * @return array{success: bool, message: string}
     */
    public function drop(?User $user, int|string $sectionId): array
    {
        if ($user !== null && is_numeric($sectionId) && $this->enrollmentService !== null) {
            try {
                $student = $user->student;
                if ($student) {
                    $enrollment = Enrollment::where('student_id', $student->id)
                        ->where('class_section_id', (int) $sectionId)
                        ->where('status', EnrollmentStatus::Registered)
                        ->first();

                    if ($enrollment) {
                        $this->enrollmentService->drop($enrollment);

                        return [
                            'success' => true,
                            'message' => 'Hủy học phần thành công.',
                        ];
                    }
                }
            } catch (BusinessRuleException $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            } catch (Throwable) {
                // Fallback mock
            }
        }

        return $this->dropMockSection((string) $sectionId);
    }

    private function getMockEnrollmentData(): object
    {
        $allSections = $this->getAllMockSections();
        $enrolledIds = session('mock_enrolled_section_ids', ['CS102.O11', 'SE201.O11']);

        $enrolledList = $allSections->filter(fn ($s) => in_array($s->id, $enrolledIds, true))->values();
        $totalCredits = $enrolledList->sum('credits');

        $availableList = $allSections->map(function ($s) use ($enrolledIds) {
            $copy = clone $s;
            $copy->is_enrolled = in_array($copy->id, $enrolledIds, true);

            return $copy;
        });

        $semester = (object) [
            'name' => 'Học kỳ 1 năm học 2026 - 2027',
            'code' => 'HK1-2026',
            'registration_open_at' => '01/11/2026 08:00',
            'registration_close_at' => '30/11/2026 17:00',
            'drop_deadline' => '05/12/2026',
            'min_credits' => 14,
            'max_credits' => 24,
            'is_open' => true,
        ];

        return (object) [
            'semester' => $semester,
            'enrolled_sections' => $enrolledList,
            'available_sections' => $availableList,
            'total_credits' => $totalCredits,
            'total_courses' => $enrolledList->count(),
            'min_credits' => 14,
            'max_credits' => 24,
        ];
    }

    private function registerMockSection(string $sectionId): array
    {
        $allSections = $this->getAllMockSections();
        $target = $allSections->firstWhere('id', $sectionId);

        if (! $target) {
            return [
                'success' => false,
                'message' => 'Lớp học phần không tồn tại hoặc đã đóng.',
            ];
        }

        $enrolledIds = session('mock_enrolled_section_ids', ['CS102.O11', 'SE201.O11']);

        // 1. Kiem tra da dang ky
        if (in_array($sectionId, $enrolledIds, true)) {
            return [
                'success' => false,
                'message' => "Bạn đã đăng ký lớp học phần {$target->code} này rồi (BR-01).",
            ];
        }

        // 2. Kiem tra si so
        if ($target->enrolled_count >= $target->capacity) {
            return [
                'success' => false,
                'message' => "Lớp học phần {$target->code} đã đủ sĩ số ({$target->capacity}/{$target->capacity}). Vui lòng chọn lớp khác.",
            ];
        }

        // 3. Kiem tra gioi han tin chi (Max 24 TC)
        $currentCredits = $allSections->filter(fn ($s) => in_array($s->id, $enrolledIds, true))->sum('credits');
        if (($currentCredits + $target->credits) > 24) {
            return [
                'success' => false,
                'message' => "Không thể đăng ký: Tổng số tín chỉ sau khi đăng ký (".($currentCredits + $target->credits)." TC) vượt quá số tín chỉ tối đa cho phép (24 TC) (BR-03).",
            ];
        }

        // Them vao session
        $enrolledIds[] = $sectionId;
        session(['mock_enrolled_section_ids' => array_values(array_unique($enrolledIds))]);

        return [
            'success' => true,
            'message' => "Đăng ký thành công lớp học phần {$target->code} - {$target->course_name} ({$target->credits} tín chỉ)!",
        ];
    }

    private function dropMockSection(string $sectionId): array
    {
        $enrolledIds = session('mock_enrolled_section_ids', ['CS102.O11', 'SE201.O11']);

        if (! in_array($sectionId, $enrolledIds, true)) {
            return [
                'success' => false,
                'message' => 'Lớp học phần không nằm trong danh sách đã đăng ký của bạn.',
            ];
        }

        $enrolledIds = array_diff($enrolledIds, [$sectionId]);
        session(['mock_enrolled_section_ids' => array_values($enrolledIds)]);

        return [
            'success' => true,
            'message' => 'Hủy đăng ký học phần thành công.',
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function getAllMockSections(): Collection
    {
        return collect([
            (object) [
                'id' => 'CS102.O11',
                'code' => 'CS102.O11',
                'course_code' => 'CS102',
                'course_name' => 'Kỹ thuật lập trình',
                'credits' => 4,
                'lecturer_name' => 'ThS. Hoàng Tuấn Anh',
                'schedule_text' => 'Thứ 2 (Tiết 1-3) · Thứ 4 (Tiết 7-9)',
                'room_name' => 'Phòng A1.02 & Lab 2',
                'capacity' => 50,
                'enrolled_count' => 48,
                'registered_at' => '05/11/2026 09:15',
            ],
            (object) [
                'id' => 'CS102.O12',
                'code' => 'CS102.O12',
                'course_code' => 'CS102',
                'course_name' => 'Kỹ thuật lập trình',
                'credits' => 4,
                'lecturer_name' => 'TS. Lê Hồng Phúc',
                'schedule_text' => 'Thứ 3 (Tiết 4-6) · Thứ 5 (Tiết 1-3)',
                'room_name' => 'Phòng A2.04 & Lab 1',
                'capacity' => 50,
                'enrolled_count' => 50,
                'registered_at' => null,
            ],
            (object) [
                'id' => 'CS201.O11',
                'code' => 'CS201.O11',
                'course_code' => 'CS201',
                'course_name' => 'Cấu trúc dữ liệu và giải thuật',
                'credits' => 4,
                'lecturer_name' => 'TS. Nguyễn Minh Châu',
                'schedule_text' => 'Thứ 2 (Tiết 4-6) · Thứ 6 (Tiết 1-3)',
                'room_name' => 'Phòng B2.01 & Lab 3',
                'capacity' => 50,
                'enrolled_count' => 42,
                'registered_at' => null,
            ],
            (object) [
                'id' => 'CS202.O11',
                'code' => 'CS202.O11',
                'course_code' => 'CS202',
                'course_name' => 'Cơ sở dữ liệu',
                'credits' => 4,
                'lecturer_name' => 'ThS. Đỗ Phương Thảo',
                'schedule_text' => 'Thứ 3 (Tiết 1-3) · Thứ 5 (Tiết 4-6)',
                'room_name' => 'Phòng A1.05 & Lab 4',
                'capacity' => 50,
                'enrolled_count' => 45,
                'registered_at' => null,
            ],
            (object) [
                'id' => 'SE201.O11',
                'code' => 'SE201.O11',
                'course_code' => 'SE201',
                'course_name' => 'Nhập môn Công nghệ phần mềm',
                'credits' => 3,
                'lecturer_name' => 'TS. Trần Văn Hùng',
                'schedule_text' => 'Thứ 4 (Tiết 1-3)',
                'room_name' => 'Phòng C1.01',
                'capacity' => 50,
                'enrolled_count' => 38,
                'registered_at' => '05/11/2026 09:20',
            ],
            (object) [
                'id' => 'SE205.O11',
                'code' => 'SE205.O11',
                'course_code' => 'SE205',
                'course_name' => 'Lập trình Web căn bản',
                'credits' => 3,
                'lecturer_name' => 'ThS. Vũ Minh Trí',
                'schedule_text' => 'Thứ 6 (Tiết 4-6)',
                'room_name' => 'Lab 1',
                'capacity' => 50,
                'enrolled_count' => 46,
                'registered_at' => null,
            ],
            (object) [
                'id' => 'MATH103.O11',
                'code' => 'MATH103.O11',
                'course_code' => 'MATH103',
                'course_name' => 'Giải tích 2',
                'credits' => 3,
                'lecturer_name' => 'TS. Phạm Thanh Sơn',
                'schedule_text' => 'Thứ 3 (Tiết 7-9)',
                'room_name' => 'Phòng B1.02',
                'capacity' => 60,
                'enrolled_count' => 55,
                'registered_at' => null,
            ],
            (object) [
                'id' => 'ENG102.O11',
                'code' => 'ENG102.O11',
                'course_code' => 'ENG102',
                'course_name' => 'Tiếng Anh học thuật 2',
                'credits' => 3,
                'lecturer_name' => 'Cô Nguyễn Bích Thủy',
                'schedule_text' => 'Thứ 5 (Tiết 7-9)',
                'room_name' => 'Phòng A3.01',
                'capacity' => 40,
                'enrolled_count' => 35,
                'registered_at' => null,
            ],
        ]);
    }

    private function formatFromDb(User $user, Collection $dbEnrollments): object
    {
        $enrolledSections = $dbEnrollments->map(fn ($e) => (object) [
            'id' => (string) $e->classSection->id,
            'code' => $e->classSection->code,
            'course_code' => $e->classSection->course->code,
            'course_name' => $e->classSection->course->name,
            'credits' => $e->classSection->course->credits,
            'lecturer_name' => $e->classSection->lecturer?->user?->name ?? 'Chưa phân công',
            'schedule_text' => 'Theo lịch phòng đào tạo',
            'room_name' => 'Phòng học chính',
            'capacity' => $e->classSection->capacity,
            'enrolled_count' => $e->classSection->enrolled_count,
            'registered_at' => $e->registered_at ? $e->registered_at->format('d/m/Y H:i') : null,
            'is_enrolled' => true,
        ]);

        $firstSection = $dbEnrollments->first()->classSection;
        $semester = $firstSection->semester;

        return (object) [
            'semester' => (object) [
                'name' => $semester->name,
                'code' => $semester->code,
                'registration_open_at' => $semester->registration_open_at?->format('d/m/Y H:i') ?? 'N/A',
                'registration_close_at' => $semester->registration_close_at?->format('d/m/Y H:i') ?? 'N/A',
                'drop_deadline' => $semester->drop_deadline?->format('d/m/Y') ?? 'N/A',
                'min_credits' => $semester->min_credits ?: 14,
                'max_credits' => $semester->max_credits ?: 24,
                'is_open' => $semester->isRegistrationOpen(),
            ],
            'enrolled_sections' => $enrolledSections,
            'available_sections' => $enrolledSections,
            'total_credits' => $enrolledSections->sum('credits'),
            'total_courses' => $enrolledSections->count(),
            'min_credits' => $semester->min_credits ?: 14,
            'max_credits' => $semester->max_credits ?: 24,
        ];
    }
}
