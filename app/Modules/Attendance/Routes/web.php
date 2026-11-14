<?php

use App\Modules\Attendance\Controllers\LecturerAttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/attendance', [LecturerAttendanceController::class, 'index'])->name('attendance');
        Route::post('/attendance/{classSession}', [LecturerAttendanceController::class, 'store'])->name('attendance.store');
    });
