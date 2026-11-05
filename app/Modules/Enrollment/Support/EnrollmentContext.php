<?php

namespace App\Modules\Enrollment\Support;

use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSection;
use Carbon\CarbonInterface;

/**
 * Du lieu dau vao cho chuoi rule kiem tra dang ky (muc 6.5).
 */
final class EnrollmentContext
{
    public function __construct(
        public readonly Student $student,
        public readonly ClassSection $classSection,
        public readonly CarbonInterface $at,
    ) {}
}
