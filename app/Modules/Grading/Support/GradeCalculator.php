<?php

namespace App\Modules\Grading\Support;

/**
 * Tinh diem tong ket va quy doi (muc 6.7). Lop thuan, thang diem truyen vao tu config('sms.grading')
 * nen kiem thu don vi khong can framework.
 *
 * Chuyen tu logic cu Grade::gradePoint()/letter() cua repo cu (nguong `>=` giu nguyen) va
 * thay cong thuc co dinh 40% GK + 60% CK bang thanh phan diem cau hinh duoc.
 */
class GradeCalculator
{
    /** @var list<array{min: float, letter: string, point: float}> */
    private array $scale;

    /**
     * @param  list<array{min: float|int, letter: string, point: float|int}>  $scale
     */
    public function __construct(
        array $scale,
        private readonly float $passMin = 4.0,
        private readonly int $decimals = 2,
    ) {
        $normalized = array_map(fn (array $row) => [
            'min' => (float) $row['min'],
            'letter' => (string) $row['letter'],
            'point' => (float) $row['point'],
        ], $scale);

        // Duyet tu nguong cao xuong thap.
        usort($normalized, fn (array $a, array $b) => $b['min'] <=> $a['min']);

        $this->scale = $normalized;
    }

    /**
     * Diem tong ket thang 10 = SUM(diem / diem toi da x 10 x trong so%).
     * Tra ve null neu con thanh phan chua co diem.
     *
     * @param  iterable<array{score: float|int|null, max: float|int, weight: float|int}>  $components
     */
    public function weightedTotal(iterable $components): ?float
    {
        $total = 0.0;
        $count = 0;

        foreach ($components as $component) {
            if ($component['score'] === null) {
                return null;
            }

            $total += ($component['score'] / $component['max']) * 10 * ($component['weight'] / 100);
            $count++;
        }

        return $count === 0 ? null : round($total, $this->decimals);
    }

    /**
     * BR-07: tong trong so cac thanh phan cua lop phai bang 100%.
     *
     * @param  iterable<float|int>  $weights
     */
    public function weightsSumTo100(iterable $weights): bool
    {
        $sum = 0.0;
        foreach ($weights as $weight) {
            $sum += $weight;
        }

        return abs($sum - 100.0) < 0.005;
    }

    public function toLetter(float $total): string
    {
        return $this->band($total)['letter'];
    }

    public function toGpa4(float $total): float
    {
        return $this->band($total)['point'];
    }

    public function isPassed(float $total): bool
    {
        return round($total, $this->decimals) >= $this->passMin;
    }

    /**
     * @return array{min: float, letter: string, point: float}
     */
    private function band(float $total): array
    {
        $total = round($total, $this->decimals);

        foreach ($this->scale as $band) {
            if ($total >= $band['min']) {
                return $band;
            }
        }

        // Thang diem hop le luon co nguong 0; day la nhanh du phong cho diem am.
        return $this->scale[array_key_last($this->scale)];
    }
}
