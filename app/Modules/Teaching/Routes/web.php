<?php

use App\Modules\Teaching\Controllers\LecturerTeachingController;
use App\Modules\Teaching\Controllers\StudentTimetableController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Teaching (lop hoc phan, thoi khoa bieu)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/timetable', [StudentTimetableController::class, 'index'])->name('timetable');
});

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/classes', [LecturerTeachingController::class, 'classes'])->name('classes');
        Route::get('/students', [LecturerTeachingController::class, 'students'])->name('students');
        Route::get('/timetable', [LecturerTeachingController::class, 'timetable'])->name('timetable');
    });
