<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\RoomFactory;
use App\Modules\Teaching\Models\ClassSchedule;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Enums\RoomType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $building
 * @property int $capacity
 * @property RoomType $type
 * @property bool $is_active
 * @property-read Collection<int, ClassSchedule> $schedules
 * @property-read Collection<int, ClassSession> $sessions
 */
class Room extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'building', 'capacity', 'type', 'is_active'];

    protected static function newFactory(): RoomFactory
    {
        return RoomFactory::new();
    }

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'type' => RoomType::class,
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<ClassSchedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /** @return HasMany<ClassSession, $this> */
    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }
}
