<?php

namespace App\Modules\Grading\Providers;

use App\Modules\Grading\Contracts\PassedCoursesProvider;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Policies\GradePolicy;
use App\Modules\Grading\Services\FinalResultPassedCoursesProvider;
use App\Modules\Grading\Support\GradeCalculator;
use App\Shared\Providers\ModuleServiceProvider;

class GradingServiceProvider extends ModuleServiceProvider
{
    protected array $policies = [
        FinalResult::class => GradePolicy::class,
    ];

    protected function moduleName(): string
    {
        return 'Grading';
    }

    public function register(): void
    {
        // Thang diem va nguong dat doc tu config('sms.grading') -> doi theo quy che truong khong can sua code.
        $this->app->singleton(GradeCalculator::class, fn () => new GradeCalculator(
            config('sms.grading.scale'),
            (float) config('sms.grading.pass_min'),
            (int) config('sms.grading.decimals'),
        ));

        $this->app->bind(PassedCoursesProvider::class, FinalResultPassedCoursesProvider::class);
    }
}
