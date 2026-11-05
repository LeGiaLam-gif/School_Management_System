<?php

namespace App\Modules\Enrollment\Services\Rules;

use App\Modules\Enrollment\Support\EnrollmentContext;
use App\Shared\Exceptions\BusinessRuleException;

/**
 * Chi dang ky khi dot dang ky cua hoc ky dang mo (muc 2.4).
 */
class RegistrationWindowRule implements EnrollmentRule
{
    public function check(EnrollmentContext $context): void
    {
        if (! $context->classSection->semester->isRegistrationOpen($context->at)) {
            throw new BusinessRuleException('Hiện không nằm trong đợt đăng ký học phần.', 'ENR-WINDOW-CLOSED');
        }
    }
}
