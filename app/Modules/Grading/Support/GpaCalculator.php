<?php

namespace App\Modules\Grading\Support;

/**
 * GPA = SUM(diem thang 4 x so tin chi) / SUM(so tin chi), chi tinh hoc phan da cong bo (muc 6.7).
 * Chuyen tu Student::gpa() cua repo cu; them chinh sach hoc lai.
 */
class GpaCalculator
{
    public const RETAKE_HIGHEST = 'highest';

    public const RETAKE_LATEST = 'latest';

    /**
     * @param  iterable<GpaEntry>  $entries
     * @param  string  $retakePolicy  highest = lan cao nhat | latest = lan gan nhat
     */
    public function calculate(iterable $entries, string $retakePolicy = self::RETAKE_HIGHEST): ?float
    {
        $chosen = [];

        foreach ($entries as $entry) {
            $current = $chosen[$entry->courseId] ?? null;

            if ($current === null || $this->prefers($entry, $current, $retakePolicy)) {
                $chosen[$entry->courseId] = $entry;
            }
        }

        $credits = 0;
        $points = 0.0;

        foreach ($chosen as $entry) {
            $credits += $entry->credits;
            $points += $entry->gpaPoint * $entry->credits;
        }

        return $credits === 0 ? null : round($points / $credits, 2);
    }

    private function prefers(GpaEntry $candidate, GpaEntry $current, string $policy): bool
    {
        if ($policy === self::RETAKE_LATEST) {
            return $candidate->publishedAt > $current->publishedAt;
        }

        // highest: diem cao hon thang; bang diem thi lan gan nhat thang.
        return $candidate->gpaPoint > $current->gpaPoint
            || ($candidate->gpaPoint === $current->gpaPoint && $candidate->publishedAt > $current->publishedAt);
    }
}
