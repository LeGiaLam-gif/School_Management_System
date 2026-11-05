<?php

namespace Tests\Unit\Grading;

use App\Modules\Grading\Support\GpaCalculator;
use App\Modules\Grading\Support\GpaEntry;
use PHPUnit\Framework\TestCase;

class GpaCalculatorTest extends TestCase
{
    public function test_gpa_is_weighted_by_credits(): void
    {
        $gpa = (new GpaCalculator)->calculate([
            new GpaEntry(1, 3, 4.0, 100),  // 12
            new GpaEntry(2, 2, 2.0, 100),  // 4
            new GpaEntry(3, 3, 3.0, 100),  // 9
        ]);

        // (12 + 4 + 9) / 8 = 3.125
        $this->assertSame(3.13, $gpa);
    }

    public function test_gpa_is_null_without_entries(): void
    {
        $this->assertNull((new GpaCalculator)->calculate([]));
    }

    public function test_failed_course_counts_as_zero_points(): void
    {
        $gpa = (new GpaCalculator)->calculate([
            new GpaEntry(1, 3, 4.0, 100),
            new GpaEntry(2, 3, 0.0, 100),
        ]);

        $this->assertSame(2.0, $gpa);
    }

    public function test_retake_highest_policy(): void
    {
        $gpa = (new GpaCalculator)->calculate([
            new GpaEntry(1, 3, 3.5, 100),
            new GpaEntry(1, 3, 2.0, 200), // hoc lai nhung thap hon
        ], GpaCalculator::RETAKE_HIGHEST);

        $this->assertSame(3.5, $gpa);
    }

    public function test_retake_latest_policy(): void
    {
        $gpa = (new GpaCalculator)->calculate([
            new GpaEntry(1, 3, 3.5, 100),
            new GpaEntry(1, 3, 2.0, 200),
        ], GpaCalculator::RETAKE_LATEST);

        $this->assertSame(2.0, $gpa);
    }

    public function test_course_counted_once_when_retaken(): void
    {
        $gpa = (new GpaCalculator)->calculate([
            new GpaEntry(1, 3, 0.0, 100),  // truot
            new GpaEntry(1, 3, 3.0, 200),  // hoc lai dat
            new GpaEntry(2, 3, 4.0, 100),
        ]);

        $this->assertSame(3.5, $gpa);
    }
}
