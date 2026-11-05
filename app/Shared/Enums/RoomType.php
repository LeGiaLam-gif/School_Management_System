<?php

namespace App\Shared\Enums;

enum RoomType: string
{
    case Lecture = 'lecture';
    case Lab = 'lab';

    public function label(): string
    {
        return match ($this) {
            self::Lecture => 'Phòng lý thuyết',
            self::Lab => 'Phòng thực hành',
        };
    }
}
