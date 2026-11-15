<?php

use App\Modules\Teaching\Controllers\LecturerTeachingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/classes', [LecturerTeachingController::class, 'classes'])->name('classes');
        Route::get('/students', [LecturerTeachingController::class, 'students'])->name('students');
        Route::get('/timetable', [LecturerTeachingController::class, 'timetable'])->name('timetable');
    });
