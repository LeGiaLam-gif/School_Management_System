<?php

namespace App\Shared\Enums;

/**
 * Vong doi dang ky (muc 9.2): registered -> dropped | completed.
 */
enum EnrollmentStatus: string
{
    case Registered = 'registered';
    case Dropped = 'dropped';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Đã đăng ký',
            self::Dropped => 'Đã hủy',
            self::Completed => 'Hoàn thành',
        };
    }
}
