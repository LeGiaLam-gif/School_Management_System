<?php

namespace App\Modules\Announcement\Providers;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Announcement\Policies\AnnouncementPolicy;
use App\Shared\Providers\ModuleServiceProvider;

class AnnouncementServiceProvider extends ModuleServiceProvider
{
    protected array $policies = [
        Announcement::class => AnnouncementPolicy::class,
    ];

    protected function moduleName(): string
    {
        return 'Announcement';
    }
}
