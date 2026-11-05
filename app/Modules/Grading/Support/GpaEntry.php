<?php

namespace App\Modules\Grading\Support;

/**
 * Mot ket qua da cong bo cua mot hoc phan, dau vao cua GpaCalculator.
 */
final class GpaEntry
{
    public function __construct(
        public readonly int $courseId,
        public readonly int $credits,
        public readonly float $gpaPoint,
        public readonly int $publishedAt, // unix timestamp, dung de chon "lan gan nhat" khi hoc lai
    ) {
    }
}
