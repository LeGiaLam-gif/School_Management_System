<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Enrollment\Services\Rules\EnrollmentRule;
use App\Modules\Enrollment\Support\EnrollmentContext;

/**
 * Chay chuoi rule theo thu tu va dung o vi pham dau tien.
 * Danh sach rule duoc rap trong EnrollmentServiceProvider.
 */
class EnrollmentRuleChecker
{
    /**
     * @param  iterable<EnrollmentRule>  $rules
     */
    public function __construct(private readonly iterable $rules) {}

    public function ensureEligible(EnrollmentContext $context): void
    {
        foreach ($this->rules as $rule) {
            $rule->check($context);
        }
    }
}
