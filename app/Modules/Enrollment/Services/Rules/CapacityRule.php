<?php

namespace App\Modules\Enrollment\Services\Rules;

use App\Modules\Enrollment\Support\EnrollmentContext;
use App\Shared\Exceptions\BusinessRuleException;

/**
 * BR-05: lop day khong nhan them. Phai chay SAU khi da lockForUpdate dong lop,
 * de `enrolled_count` la gia tri moi nhat.
 */
class CapacityRule implements EnrollmentRule
{
    public function check(EnrollmentContext $context): void
    {
        if ($context->classSection->isFull()) {
            throw new BusinessRuleException('Lớp học phần đã đủ sĩ số.', 'BR-05');
        }
    }
}
