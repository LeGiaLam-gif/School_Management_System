<?php

namespace Tests\Unit\Attendance;

use App\Modules\Attendance\Support\AbsenceRateCalculator;
use PHPUnit\Framework\TestCase;

class AbsenceRateCalculatorTest extends TestCase
{
    public function test_rate_is_absent_over_held(): void
    {
        $this->assertSame(0.2, (new AbsenceRateCalculator)->rate(3, 15));
    }

    public function test_rate_is_zero_without_held_sessions(): void
    {
        $this->assertSame(0.0, (new AbsenceRateCalculator)->rate(0, 0));
    }

    public function test_threshold_is_strictly_greater_than(): void
    {
        $calc = new AbsenceRateCalculator;

        // Dung bang 20% chua bi canh bao (BR-09: "vuot nguong").
        $this->assertFalse($calc->exceeds(2, 10, 0.20));
        $this->assertTrue($calc->exceeds(3, 10, 0.20));
    }
}
