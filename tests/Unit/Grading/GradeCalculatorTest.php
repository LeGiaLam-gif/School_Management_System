<?php

namespace Tests\Unit\Grading;

use App\Modules\Grading\Support\GradeCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GradeCalculatorTest extends TestCase
{
    private function calculator(): GradeCalculator
    {
        return new GradeCalculator([
            ['min' => 8.5, 'letter' => 'A', 'point' => 4.0],
            ['min' => 8.0, 'letter' => 'B+', 'point' => 3.5],
            ['min' => 7.0, 'letter' => 'B', 'point' => 3.0],
            ['min' => 6.5, 'letter' => 'C+', 'point' => 2.5],
            ['min' => 5.5, 'letter' => 'C', 'point' => 2.0],
            ['min' => 5.0, 'letter' => 'D+', 'point' => 1.5],
            ['min' => 4.0, 'letter' => 'D', 'point' => 1.0],
            ['min' => 0.0, 'letter' => 'F', 'point' => 0.0],
        ], 4.0, 2);
    }

    /**
     * @return array<string, array{float, string, float, bool}>
     */
    public static function bands(): array
    {
        return [
            'A tren' => [10.0, 'A', 4.0, true],
            'A bien' => [8.5, 'A', 4.0, true],
            'B+ tren bien A' => [8.49, 'B+', 3.5, true],
            'B+ bien' => [8.0, 'B+', 3.5, true],
            'B bien' => [7.0, 'B', 3.0, true],
            'C+ bien' => [6.5, 'C+', 2.5, true],
            'C bien' => [5.5, 'C', 2.0, true],
            'D+ bien' => [5.0, 'D+', 1.5, true],
            'D bien dat' => [4.0, 'D', 1.0, true],
            'F sat bien' => [3.99, 'F', 0.0, false],
            'F 0' => [0.0, 'F', 0.0, false],
        ];
    }

    #[DataProvider('bands')]
    public function test_conversion(float $total, string $letter, float $point, bool $passed): void
    {
        $c = $this->calculator();

        $this->assertSame($letter, $c->toLetter($total));
        $this->assertSame($point, $c->toGpa4($total));
        $this->assertSame($passed, $c->isPassed($total));
    }

    public function test_weighted_total_matches_legacy_40_60_formula(): void
    {
        // Cong thuc repo cu: 40% giua ky + 60% cuoi ky.
        $total = $this->calculator()->weightedTotal([
            ['score' => 7.0, 'max' => 10, 'weight' => 40],
            ['score' => 9.0, 'max' => 10, 'weight' => 60],
        ]);

        $this->assertSame(8.2, $total);
    }

    public function test_weighted_total_normalises_by_max_score(): void
    {
        $total = $this->calculator()->weightedTotal([
            ['score' => 15, 'max' => 20, 'weight' => 50], // 7.5/10
            ['score' => 8, 'max' => 10, 'weight' => 50],
        ]);

        $this->assertSame(7.75, $total);
    }

    public function test_weighted_total_is_null_when_a_component_is_missing(): void
    {
        $total = $this->calculator()->weightedTotal([
            ['score' => 7.0, 'max' => 10, 'weight' => 40],
            ['score' => null, 'max' => 10, 'weight' => 60],
        ]);

        $this->assertNull($total);
    }

    public function test_weighted_total_is_null_without_components(): void
    {
        $this->assertNull($this->calculator()->weightedTotal([]));
    }

    public function test_rounding_prevents_float_noise_at_band_boundary(): void
    {
        // 8.45 va 8.5 khong bi lech do sai so dau phay dong.
        $total = $this->calculator()->weightedTotal([
            ['score' => 8.5, 'max' => 10, 'weight' => 100],
        ]);

        $this->assertSame('A', $this->calculator()->toLetter($total));
    }

    public function test_weights_must_sum_to_100(): void
    {
        $c = $this->calculator();

        $this->assertTrue($c->weightsSumTo100([10, 30, 60]));
        $this->assertTrue($c->weightsSumTo100([33.33, 33.33, 33.34]));
        $this->assertFalse($c->weightsSumTo100([40, 50]));
        $this->assertFalse($c->weightsSumTo100([]));
    }

    public function test_scale_order_in_config_does_not_matter(): void
    {
        $shuffled = new GradeCalculator([
            ['min' => 0.0, 'letter' => 'F', 'point' => 0.0],
            ['min' => 8.5, 'letter' => 'A', 'point' => 4.0],
            ['min' => 4.0, 'letter' => 'D', 'point' => 1.0],
        ]);

        $this->assertSame('A', $shuffled->toLetter(9.0));
        $this->assertSame('D', $shuffled->toLetter(6.0));
        $this->assertSame('F', $shuffled->toLetter(2.0));
    }
}
