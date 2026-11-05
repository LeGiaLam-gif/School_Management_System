<?php

namespace App\Modules\Teaching\Support;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * Mot khung lich hoc lap hang tuan (thu + tiet + khoang ngay).
 *
 * Lop thuan (khong phu thuoc DB) cho "thanh phan de sai nhat" (muc 6.4): dung chung cho
 * phat hien xung dot phong/giang vien (BR-06) va trung lich cua sinh vien (BR-04).
 */
final class ScheduleSlot
{
    public function __construct(
        public readonly int $dayOfWeek,      // ISO-8601: 1 = Thu hai ... 7 = Chu nhat
        public readonly int $startPeriod,
        public readonly int $endPeriod,
        public readonly DateTimeInterface $startDate,
        public readonly DateTimeInterface $endDate,
    ) {
        if ($dayOfWeek < 1 || $dayOfWeek > 7) {
            throw new InvalidArgumentException('day_of_week phải nằm trong khoảng 1..7 (ISO-8601).');
        }

        if ($startPeriod > $endPeriod) {
            throw new InvalidArgumentException('Tiết bắt đầu phải nhỏ hơn hoặc bằng tiết kết thúc.');
        }

        if ($startDate->format('Y-m-d') > $endDate->format('Y-m-d')) {
            throw new InvalidArgumentException('Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.');
        }
    }

    /**
     * Xung dot khi: cung thu, giao nhau ve tiet VA giao nhau ve khoang ngay.
     * Hai khung chi sat nhau (tiet 1-3 va tiet 4-6) KHONG tinh la trung.
     */
    public function conflictsWith(self $other): bool
    {
        return $this->dayOfWeek === $other->dayOfWeek
            && $this->startPeriod <= $other->endPeriod
            && $other->startPeriod <= $this->endPeriod
            && $this->startDate->format('Y-m-d') <= $other->endDate->format('Y-m-d')
            && $other->startDate->format('Y-m-d') <= $this->endDate->format('Y-m-d');
    }
}
