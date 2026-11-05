<?php

namespace App\Shared\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case Excused = 'excused';

    /**
     * Ti le vang = so buoi absent (khong tinh excused) / so buoi da dien ra (muc 6.6).
     */
    public function countsAsAbsence(): bool
    {
        return $this === self::Absent;
    }

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Có mặt',
            self::Absent => 'Vắng',
            self::Late => 'Muộn',
            self::Excused => 'Có phép',
        };
    }
}
