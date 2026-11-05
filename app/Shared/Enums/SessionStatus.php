<?php

namespace App\Shared\Enums;

enum SessionStatus: string
{
    case Scheduled = 'scheduled';
    case Held = 'held';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Chưa diễn ra',
            self::Held => 'Đã diễn ra',
            self::Cancelled => 'Đã hủy',
        };
    }
}
