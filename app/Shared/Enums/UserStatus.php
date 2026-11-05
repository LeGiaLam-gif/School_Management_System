<?php

namespace App\Shared\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Locked = 'locked';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Hoạt động',
            self::Locked => 'Đã khóa',
        };
    }
}
