<?php

namespace App\Modules\Identity\Providers;

use App\Modules\Identity\Models\User;
use App\Modules\Identity\Policies\UserPolicy;
use App\Shared\Providers\ModuleServiceProvider;

class IdentityServiceProvider extends ModuleServiceProvider
{
    protected array $policies = [
        User::class => UserPolicy::class,
    ];

    protected function moduleName(): string
    {
        return 'Identity';
    }
}
