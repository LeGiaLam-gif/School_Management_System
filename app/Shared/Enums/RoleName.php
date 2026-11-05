<?php

namespace App\Shared\Enums;

/**
 * Ten role luu trong Spatie Permission (khong dung cot `type`/`role` cung tren users).
 */
enum RoleName: string
{
    case Admin = 'admin';
    case Lecturer = 'lecturer';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Quản trị viên',
            self::Lecturer => 'Giảng viên',
            self::Student => 'Sinh viên',
        };
    }
}
