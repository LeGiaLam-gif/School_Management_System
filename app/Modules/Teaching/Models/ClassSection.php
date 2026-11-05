<?php

namespace App\Modules\Teaching\Models;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Semester;
use App\Modules\Announcement\Models\Announcement;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\GradeComponent;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Database\Factories\ClassSectionFactory;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id', 'semester_id', 'lecturer_id', 'code', 'capacity', 'enrolled_count', 'status',
    ];

    protected static function newFactory(): ClassSectionFactory
    {
        return ClassSectionFactory::new();
    }

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'enrolled_count' => 'integer',
            'status' => ClassSectionStatus::class,
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function gradeComponents(): HasMany
    {
        return $this->hasMany(GradeComponent::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Giang vien phu trach lop? Dung chung cho Policy cua Attendance/Grading/Announcement.
     */
    public function isTaughtBy(User $user): bool
    {
        $lecturerId = $user->lecturer?->id;

        return $lecturerId !== null && $lecturerId === $this->lecturer_id;
    }

    /**
     * Doc tu cot phi chuan enrolled_count (BR-05); luon cap nhat trong transaction co khoa dong.
     */
    public function isFull(): bool
    {
        return $this->enrolled_count >= $this->capacity;
    }

    public function remainingSeats(): int
    {
        return max(0, $this->capacity - $this->enrolled_count);
    }
}
