<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\RoomFactory;
use App\Shared\Enums\RoomType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
