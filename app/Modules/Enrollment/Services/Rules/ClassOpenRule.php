<?php

namespace App\Modules\Enrollment\Services\Rules;

use App\Modules\Enrollment\Support\EnrollmentContext;
use App\Shared\Exceptions\BusinessRuleException;

/**
 * Chi lop o trang thai `open` moi nhan dang ky (FR-CLS-05).
 */
class ClassOpenRule implements EnrollmentRule
{
    public function check(EnrollmentContext $context): void
    {
        if (! $context->classSection->status->acceptsEnrollment()) {
            throw new BusinessRuleException('Lớp học phần chưa mở đăng ký hoặc đã đóng.', 'ENR-CLASS-NOT-OPEN');
        }
    }
}
