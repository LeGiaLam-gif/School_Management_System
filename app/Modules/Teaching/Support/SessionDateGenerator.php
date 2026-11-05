<?php

namespace App\Modules\Teaching\Support;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Sinh cac ngay hoc tu lich lap hang tuan (FR-CLS-04).
 */
class SessionDateGenerator
{
    /**
     * @param  int  $isoDay  1 = Thu hai ... 7 = Chu nhat
     * @return list<string> cac ngay Y-m-d tang dan, bao gom ca hai dau mut
     */
    public function dates(DateTimeInterface $start, DateTimeInterface $end, int $isoDay): array
    {
        if ($isoDay < 1 || $isoDay > 7) {
            throw new InvalidArgumentException('day_of_week phải nằm trong khoảng 1..7 (ISO-8601).');
        }

        $cursor = DateTimeImmutable::createFromInterface($start)->setTime(0, 0);
        $last = DateTimeImmutable::createFromInterface($end)->setTime(0, 0);

        $offset = ($isoDay - (int) $cursor->format('N') + 7) % 7;
        $cursor = $cursor->modify("+{$offset} days");

        $dates = [];
        while ($cursor <= $last) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor = $cursor->modify('+7 days');
        }

        return $dates;
    }
}
