<?php

namespace App\Shared\Enums;

enum AnnouncementScope: string
{
    case All = 'all';
    case ClassSection = 'class_section';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Toàn trường',
            self::ClassSection => 'Theo lớp học phần',
        };
    }
}
