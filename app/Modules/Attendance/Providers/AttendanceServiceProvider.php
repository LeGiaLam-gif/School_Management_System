<?php

namespace App\Modules\Attendance\Providers;

use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Policies\AttendancePolicy;
use App\Shared\Providers\ModuleServiceProvider;

class AttendanceServiceProvider extends ModuleServiceProvider
{
    protected array $policies = [
        AttendanceRecord::class => AttendancePolicy::class,
    ];

    protected function moduleName(): string
    {
        return 'Attendance';
    }
}
