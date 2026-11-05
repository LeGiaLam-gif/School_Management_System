<?php

namespace Tests\Unit\Teaching;

use App\Modules\Teaching\Support\ScheduleSlot;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ScheduleSlotTest extends TestCase
{
    private function slot(int $day, int $start, int $end, string $from = '2026-09-07', string $to = '2026-12-27'): ScheduleSlot
    {
        return new ScheduleSlot($day, $start, $end, new DateTimeImmutable($from), new DateTimeImmutable($to));
    }

    public function test_overlapping_periods_same_day_conflict(): void
    {
        $this->assertTrue($this->slot(1, 1, 3)->conflictsWith($this->slot(1, 3, 5)));
        $this->assertTrue($this->slot(1, 2, 4)->conflictsWith($this->slot(1, 1, 6)));
    }

    public function test_adjacent_periods_do_not_conflict(): void
    {
        // Bien: tiet 1-3 va tiet 4-6 sat nhau, khong trung (muc 6.4).
        $this->assertFalse($this->slot(1, 1, 3)->conflictsWith($this->slot(1, 4, 6)));
        $this->assertFalse($this->slot(1, 4, 6)->conflictsWith($this->slot(1, 1, 3)));
    }

    public function test_single_period_slots(): void
    {
        $this->assertTrue($this->slot(2, 5, 5)->conflictsWith($this->slot(2, 5, 5)));
        $this->assertFalse($this->slot(2, 5, 5)->conflictsWith($this->slot(2, 6, 6)));
    }

    public function test_different_day_never_conflicts(): void
    {
        $this->assertFalse($this->slot(1, 1, 3)->conflictsWith($this->slot(2, 1, 3)));
    }

    public function test_disjoint_date_ranges_do_not_conflict(): void
    {
        $a = $this->slot(1, 1, 3, '2026-09-01', '2026-10-15');
        $b = $this->slot(1, 1, 3, '2026-10-16', '2026-12-20');

        $this->assertFalse($a->conflictsWith($b));
    }

    public function test_date_ranges_touching_on_one_day_conflict(): void
    {
        $a = $this->slot(1, 1, 3, '2026-09-01', '2026-10-15');
        $b = $this->slot(1, 1, 3, '2026-10-15', '2026-12-20');

        $this->assertTrue($a->conflictsWith($b));
    }

    public function test_conflict_is_symmetric(): void
    {
        $a = $this->slot(3, 2, 4);
        $b = $this->slot(3, 4, 6);

        $this->assertSame($a->conflictsWith($b), $b->conflictsWith($a));
    }

    public function test_rejects_invalid_day(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->slot(8, 1, 3);
    }

    public function test_rejects_inverted_periods(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->slot(1, 5, 3);
    }

    public function test_rejects_inverted_dates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->slot(1, 1, 3, '2026-12-01', '2026-09-01');
    }
}
