<?php

namespace App\Modules\Reporting\Providers;

use App\Shared\Providers\ModuleServiceProvider;

/**
 * Reporting & Audit: chi DOC du lieu cac module khac (dashboard, bao cao, xem audit log).
 * Viec GHI audit nam o App\Shared\Audit (xem docs/03-architecture/decisions.md, ADR-006).
 */
class ReportingServiceProvider extends ModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Reporting';
    }
}
