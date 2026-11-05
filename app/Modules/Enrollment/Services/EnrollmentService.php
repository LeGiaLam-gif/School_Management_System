<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Enrollment\Events\EnrollmentCreated;
use App\Modules\Enrollment\Events\EnrollmentDropped;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Support\EnrollmentContext;
use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Module quan trong nhat (muc 6.5). Luong register:
 * transaction -> khoa dong lop -> chay rule -> tao enrollment -> tang enrolled_count -> commit -> phat event.
 *
 * Con thieu (Sprint 3, xem docs/04-team/progress.md): PrerequisiteRule (BR-02),
 * DuplicateCourseRule (BR-01), ScheduleConflictRule (BR-04), CreditLimitRule (BR-03), adminOverride.
 */
class EnrollmentService
{
    public function __construct(private readonly EnrollmentRuleChecker $rules) {}

    public function register(Student $student, int $classSectionId): Enrollment
    {
        try {
            $enrollment = DB::transaction(function () use ($student, $classSectionId) {
                // Khoa dong de 2 request dong thoi khong cung thay con 1 cho (NFR-DATA-01).
                $section = ClassSection::query()
                    ->with('semester')
                    ->lockForUpdate()
                    ->findOrFail($classSectionId);

                $this->rules->ensureEligible(new EnrollmentContext($student, $section, now()));

                $enrollment = Enrollment::create([
                    'student_id' => $student->id,
                    'class_section_id' => $section->id,
                    'status' => EnrollmentStatus::Registered,
                    'registered_at' => now(),
                ]);

                $section->increment('enrolled_count');

                return $enrollment;
            });
        } catch (UniqueConstraintViolationException) {
            // Chot chan cuoi cung o tang DB: unique (student_id, class_section_id).
            throw new BusinessRuleException('Bạn đã đăng ký lớp học phần này.', 'BR-01');
        }

        EnrollmentCreated::dispatch($enrollment);

        return $enrollment;
    }

    public function drop(Enrollment $enrollment): Enrollment
    {
        DB::transaction(function () use ($enrollment) {
            $section = ClassSection::query()
                ->with('semester')
                ->lockForUpdate()
                ->findOrFail($enrollment->class_section_id);

            $enrollment->refresh();

            if ($enrollment->status !== EnrollmentStatus::Registered) {
                throw new BusinessRuleException('Chỉ có thể hủy đăng ký đang ở trạng thái đã đăng ký.', 'ENR-DROP-STATE');
            }

            if (! $section->semester->canDropOn(now())) {
                throw new BusinessRuleException('Đã quá hạn rút học phần.', 'ENR-DROP-DEADLINE');
            }

            // Ban ghi dropped duoc giu lai de truy vet, khong xoa (muc 9.2).
            $enrollment->update([
                'status' => EnrollmentStatus::Dropped,
                'dropped_at' => now(),
            ]);

            $section->decrement('enrolled_count');
        });

        EnrollmentDropped::dispatch($enrollment);

        return $enrollment;
    }
}
