<?php

namespace App\Modules\Attendance\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sua diem danh da chot phai co ly do va ghi audit (BR-10, FR-ATT-04):
 * thay doi qua model duoc ghi tu dong boi trait Auditable. Ghi hang loat bang
 * upsert() khong kich hoat event nen AttendanceService phai goi AuditLogger thu cong.
 */
class AttendanceRecord extends Model
{
    use Auditable;

    protected $fillable = [
        'class_session_id', 'enrollment_id', 'status', 'note', 'marked_by', 'marked_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'marked_at' => 'datetime',
        ];
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
