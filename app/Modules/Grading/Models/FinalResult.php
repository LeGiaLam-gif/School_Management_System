<?php

namespace App\Modules\Grading\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\User;
use App\Shared\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ket qua tong ket. Moi thay doi (diem, mo khoa) duoc audit tu dong (BR-10).
 */
class FinalResult extends Model
{
    use Auditable;

    protected $fillable = [
        'enrollment_id', 'total_score', 'letter_grade', 'gpa_point', 'is_passed',
        'published_at', 'locked_at', 'unlocked_by', 'unlock_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'float',
            'gpa_point' => 'float',
            'is_passed' => 'boolean',
            'published_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function unlockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    /**
     * Chi ket qua da cong bo moi duoc sinh vien thay va tinh vao GPA (BR-08).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }
}
