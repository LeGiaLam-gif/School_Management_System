<?php

namespace App\Modules\Teaching\Models;

use App\Modules\Academic\Models\Room;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Shared\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    protected $fillable = [
        'class_section_id', 'class_schedule_id', 'session_no', 'session_date',
        'start_period', 'end_period', 'room_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'session_no' => 'integer',
            'session_date' => 'date',
            'start_period' => 'integer',
            'end_period' => 'integer',
            'status' => SessionStatus::class,
        ];
    }

    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
