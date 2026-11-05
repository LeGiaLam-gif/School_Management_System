<?php

namespace App\Modules\Teaching\Providers;

use App\Modules\Teaching\Models\ClassSection;
use App\Modules\Teaching\Policies\ClassSectionPolicy;
use App\Shared\Providers\ModuleServiceProvider;

class TeachingServiceProvider extends ModuleServiceProvider
{
    protected array $policies = [
        ClassSection::class => ClassSectionPolicy::class,
    ];

    protected function moduleName(): string
    {
        return 'Teaching';
    }
}
