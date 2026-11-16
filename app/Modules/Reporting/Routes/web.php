<?php

use App\Modules\Reporting\Controllers\LecturerReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/reports', [LecturerReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [LecturerReportController::class, 'exportCsv'])->name('reports.export');
    });
