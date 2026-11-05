<?php

namespace App\Shared\Enums;

/**
 * Vong doi lop hoc phan (muc 9.1):
 * draft -> open -> in_progress -> finished -> grades_published,
 * va chieu nguoc DUY NHAT grades_published -> finished (Admin mo khoa diem, bat buoc ly do).
 */
enum ClassSectionStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case InProgress = 'in_progress';
    case Finished = 'finished';
    case GradesPublished = 'grades_published';

    /**
     * @return list<self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Draft => [self::Open],
            self::Open => [self::InProgress],
            self::InProgress => [self::Finished],
            self::Finished => [self::GradesPublished],
            self::GradesPublished => [self::Finished],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNext(), true);
    }

    /**
     * Chuyen nguoc (mo khoa diem) chi Admin duoc lam va phai ghi audit.
     */
    public function isUnlockTransition(self $next): bool
    {
        return $this === self::GradesPublished && $next === self::Finished;
    }

    /**
     * Sinh vien chi dang ky duoc khi lop dang mo.
     */
    public function acceptsEnrollment(): bool
    {
        return $this === self::Open;
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::Open => 'Mở đăng ký',
            self::InProgress => 'Đang học',
            self::Finished => 'Kết thúc',
            self::GradesPublished => 'Đã công bố điểm',
        };
    }
}
