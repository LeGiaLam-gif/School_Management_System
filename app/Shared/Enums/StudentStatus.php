<?php

namespace App\Shared\Enums;

enum StudentStatus: string
{
    case Studying = 'studying';
    case Suspended = 'suspended';
    case Graduated = 'graduated';
    case DroppedOut = 'dropped_out';

    public function label(): string
    {
        return match ($this) {
            self::Studying => 'Đang học',
            self::Suspended => 'Bảo lưu',
            self::Graduated => 'Đã tốt nghiệp',
            self::DroppedOut => 'Thôi học',
        };
    }
}
