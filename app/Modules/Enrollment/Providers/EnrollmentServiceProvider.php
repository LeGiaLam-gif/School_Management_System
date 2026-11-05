<?php

namespace App\Modules\Enrollment\Providers;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Policies\EnrollmentPolicy;
use App\Modules\Enrollment\Services\EnrollmentRuleChecker;
use App\Modules\Enrollment\Services\Rules\CapacityRule;
use App\Modules\Enrollment\Services\Rules\ClassOpenRule;
use App\Modules\Enrollment\Services\Rules\RegistrationWindowRule;
use App\Shared\Providers\ModuleServiceProvider;

class EnrollmentServiceProvider extends ModuleServiceProvider
{
    protected array $policies = [
        Enrollment::class => EnrollmentPolicy::class,
    ];

    protected function moduleName(): string
    {
        return 'Enrollment';
    }

    public function register(): void
    {
        // Them/bot rule = sua danh sach nay (thu tu = thu tu kiem tra).
        $this->app->bind(EnrollmentRuleChecker::class, fn ($app) => new EnrollmentRuleChecker([
            $app->make(ClassOpenRule::class),
            $app->make(RegistrationWindowRule::class),
            $app->make(CapacityRule::class),
        ]));
    }
}
