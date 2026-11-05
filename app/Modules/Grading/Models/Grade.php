<?php

namespace App\Modules\Grading\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Diem tho cua mot thanh phan. Diem tong ket KHONG tinh o day (repo cu tinh trong saving hook)
 * ma do GradeCalculator tinh va luu o final_results (muc 6.7).
 */
class Grade extends Model
{
    protected $fillable = ['enrollment_id', 'grade_component_id', 'score', 'graded_by', 'graded_at'];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'graded_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(GradeComponent::class, 'grade_component_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
