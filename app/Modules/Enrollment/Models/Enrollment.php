<?php

namespace App\Modules\Enrollment\Models;

use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Enrollment\Database\Factories\EnrollmentFactory;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Models\Grade;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'class_section_id', 'status', 'registered_at', 'dropped_at',
        'registered_by', 'override_reason',
    ];

    protected static function newFactory(): EnrollmentFactory
    {
        return EnrollmentFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status' => EnrollmentStatus::class,
            'registered_at' => 'datetime',
            'dropped_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function registrar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function finalResult(): HasOne
    {
        return $this->hasOne(FinalResult::class);
    }

    /**
     * Dang ky con hieu luc (chua huy): giu cho trong lop.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [EnrollmentStatus::Registered->value, EnrollmentStatus::Completed->value]);
    }

    public function scopeRegistered(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Registered->value);
    }
}
