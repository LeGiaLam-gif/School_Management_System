<?php

namespace Tests\Unit\Teaching;

use App\Modules\Teaching\Support\SessionDateGenerator;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SessionDateGeneratorTest extends TestCase
{
    public function test_generates_weekly_dates_on_the_requested_weekday(): void
    {
        // 2026-09-07 la Thu hai.
        $dates = (new SessionDateGenerator)->dates(
            new DateTimeImmutable('2026-09-07'),
            new DateTimeImmutable('2026-09-28'),
            1,
        );

        $this->assertSame(['2026-09-07', '2026-09-14', '2026-09-21', '2026-09-28'], $dates);
    }

    public function test_first_date_moves_forward_to_the_weekday(): void
    {
        // Bat dau Thu hai 2026-09-07, hoc Thu tu (ISO 3).
        $dates = (new SessionDateGenerator)->dates(
            new DateTimeImmutable('2026-09-07'),
            new DateTimeImmutable('2026-09-20'),
            3,
        );

        $this->assertSame(['2026-09-09', '2026-09-16'], $dates);
    }

    public function test_sunday_is_iso_7(): void
    {
        $dates = (new SessionDateGenerator)->dates(
            new DateTimeImmutable('2026-09-07'),
            new DateTimeImmutable('2026-09-20'),
            7,
        );

        $this->assertSame(['2026-09-13', '2026-09-20'], $dates);
    }

    public function test_empty_when_no_matching_day_in_range(): void
    {
        $dates = (new SessionDateGenerator)->dates(
            new DateTimeImmutable('2026-09-07'),
            new DateTimeImmutable('2026-09-08'),
            5,
        );

        $this->assertSame([], $dates);
    }

    public function test_rejects_invalid_weekday(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new SessionDateGenerator)->dates(new DateTimeImmutable('2026-09-07'), new DateTimeImmutable('2026-09-20'), 0);
    }
}
