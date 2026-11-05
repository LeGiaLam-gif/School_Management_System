<?php

namespace App\Shared\Enums;

/**
 * Loai hoc phan trong khung chuong trinh (program_courses.type).
 */
enum CourseType: string
{
    case Required = 'required';
    case Elective = 'elective';

    public function label(): string
    {
        return match ($this) {
            self::Required => 'Bắt buộc',
            self::Elective => 'Tự chọn',
        };
    }
}
