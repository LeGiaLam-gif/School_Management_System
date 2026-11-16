<?php

use App\Modules\Attendance\Controllers\LecturerAttendanceController;
use App\Modules\Attendance\Controllers\StudentAttendanceController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Attendance (diem danh, chuyen can)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance');
});

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/attendance', [LecturerAttendanceController::class, 'index'])->name('attendance');
        Route::post('/attendance/{classSession}', [LecturerAttendanceController::class, 'store'])->name('attendance.store');
    });
