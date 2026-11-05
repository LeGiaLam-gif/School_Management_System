<?php

namespace App\Modules\Teaching\Models;

use App\Modules\Academic\Models\Room;
use App\Modules\Teaching\Database\Factories\ClassScheduleFactory;
use App\Modules\Teaching\Support\ScheduleSlot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_section_id', 'room_id', 'day_of_week', 'start_period', 'end_period', 'start_date', 'end_date',
    ];

    protected static function newFactory(): ClassScheduleFactory
    {
        return ClassScheduleFactory::new();
    }

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'start_period' => 'integer',
            'end_period' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function toSlot(): ScheduleSlot
    {
        return new ScheduleSlot(
            $this->day_of_week,
            $this->start_period,
            $this->end_period,
            $this->start_date,
            $this->end_date,
        );
    }
}
