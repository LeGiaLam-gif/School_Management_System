<?php

namespace App\Modules\Attendance\Support;

/**
 * Ti le vang (muc 6.6): so buoi absent (KHONG tinh excused) / so buoi da dien ra.
 * Lop thuan, khong phu thuoc DB.
 */
class AbsenceRateCalculator
{
    public function rate(int $absentSessions, int $heldSessions): float
    {
        if ($heldSessions <= 0) {
            return 0.0;
        }

        return $absentSessions / $heldSessions;
    }

    /**
     * Vuot nguong khi ti le LON HON nguong (mac dinh 20%, BR-09).
     */
    public function exceeds(int $absentSessions, int $heldSessions, float $threshold): bool
    {
        return $this->rate($absentSessions, $heldSessions) > $threshold;
    }
}
