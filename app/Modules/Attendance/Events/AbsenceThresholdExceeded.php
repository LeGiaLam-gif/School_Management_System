<?php

namespace App\Modules\Attendance\Events;

use App\Modules\Enrollment\Models\Enrollment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BR-09: vang vuot nguong -> canh bao sinh vien va giang vien.
 */
class AbsenceThresholdExceeded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Enrollment $enrollment,
        public readonly float $rate,
    ) {}
}
