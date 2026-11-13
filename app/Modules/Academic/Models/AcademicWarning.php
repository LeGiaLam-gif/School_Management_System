<?php

namespace App\Modules\Academic\Models;

use App\Modules\Identity\Models\Student;
use App\Shared\Enums\ViolationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cảnh báo học vụ theo Thông tư 08/2021/TT-BGDĐT.
 *
 * @property int $id
 * @property int $student_id
 * @property int $semester_id
 * @property int $warning_level 1 = lần 1 | 2 = lần 2 | 3 = buộc thôi học
 * @property int $student_year Năm học sinh viên (1–4+)
 * @property float $semester_gpa ĐTBHK – thang điểm 4
 * @property float $cumulative_gpa ĐTBCTL – thang điểm 4
 * @property int $registered_credits
 * @property int $failed_credits
 * @property int $total_debt_credits
 * @property ViolationType $violation_type
 * @property string $reason
 * @property string $status active | resolved | dismissed
 * @property-read Student     $student
 * @property-read Semester    $semester
 * @property-read string      $violation_title   Nhãn tiếng Việt của loại vi phạm
 * @property-read string      $warning_level_label
 */
class AcademicWarning extends Model
{
    protected $fillable = [
        'student_id',
        'semester_id',
        'warning_level',
        'student_year',
        'semester_gpa',
        'cumulative_gpa',
        'registered_credits',
        'failed_credits',
        'total_debt_credits',
        'violation_type',
        'reason',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'warning_level' => 'integer',
            'student_year' => 'integer',
            'semester_gpa' => 'float',
            'cumulative_gpa' => 'float',
            'registered_credits' => 'integer',
            'failed_credits' => 'integer',
            'total_debt_credits' => 'integer',
            'violation_type' => ViolationType::class,
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    /** @return BelongsTo<Student, $this> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return BelongsTo<Semester, $this> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /** Chỉ lấy cảnh báo đang có hiệu lực */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** Cảnh báo nghiêm trọng (lần 2 trở lên) */
    public function scopeSevere(Builder $query): Builder
    {
        return $query->where('warning_level', '>=', 2);
    }

    /** Sinh viên có nguy cơ bị buộc thôi học */
    public function scopeExpulsionRisk(Builder $query): Builder
    {
        return $query->where('warning_level', 3);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Nhãn tiếng Việt của loại vi phạm.
     */
    public function getViolationTitleAttribute(): string
    {
        return $this->violation_type instanceof ViolationType
            ? $this->violation_type->label()
            : '';
    }

    /**
     * Nhãn hiển thị mức cảnh báo.
     */
    public function getWarningLevelLabelAttribute(): string
    {
        return match ($this->warning_level) {
            1 => 'Cảnh báo lần 1',
            2 => 'Cảnh báo lần 2',
            3 => 'Buộc thôi học',
            default => "Cảnh báo mức {$this->warning_level}",
        };
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Sinh viên có nguy cơ bị buộc thôi học không? */
    public function isExpulsionRisk(): bool
    {
        return $this->warning_level >= 3;
    }

    /** Cảnh báo còn hiệu lực không? */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ─── Static helpers (TT08/2021 thresholds) ───────────────────────────────

    /**
     * Ngưỡng ĐTBHK tối thiểu theo kỳ học.
     * Kỳ 1: 0.80 | Kỳ ≥ 2: 1.00  (Khoản 1 Điều 16 TT08/2021)
     */
    public static function semesterGpaThreshold(bool $isFirstSemester): float
    {
        return $isFirstSemester ? 0.80 : 1.00;
    }

    /**
     * Ngưỡng ĐTBCTL tối thiểu theo năm học.
     * Năm 1: 1.20 | Năm 2: 1.40 | Năm 3: 1.60 | Năm 4+: 1.80
     * (Khoản 2 Điều 16 TT08/2021)
     */
    public static function cumulativeGpaThreshold(int $studentYear): float
    {
        return match (true) {
            $studentYear <= 1 => 1.20,
            $studentYear === 2 => 1.40,
            $studentYear === 3 => 1.60,
            default => 1.80,
        };
    }
}
