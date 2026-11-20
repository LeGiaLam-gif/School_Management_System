<?php

use App\Modules\Reporting\Controllers\AdminReportController;
use App\Modules\Reporting\Controllers\LecturerReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/reports', [LecturerReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [LecturerReportController::class, 'exportCsv'])->name('reports.export');
    });

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('reports.export');
    });
