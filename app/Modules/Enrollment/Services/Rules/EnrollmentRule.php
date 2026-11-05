<?php

namespace App\Modules\Enrollment\Services\Rules;

use App\Modules\Enrollment\Support\EnrollmentContext;
use App\Shared\Exceptions\BusinessRuleException;

/**
 * Mot quy tac dang ky doc lap, de them/bot (muc 6.5). Moi rule co test rieng.
 */
interface EnrollmentRule
{
    /**
     * @throws BusinessRuleException khi vi pham
     */
    public function check(EnrollmentContext $context): void;
}
