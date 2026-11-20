<?php

use App\Modules\Grading\Controllers\LecturerGradeController;
use App\Modules\Grading\Controllers\StudentGradeController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Grading (bang diem, GPA)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades');
});

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/grades', [LecturerGradeController::class, 'index'])->name('grades');
        Route::post('/grades/{classSection}', [LecturerGradeController::class, 'saveGrades'])->name('grades.store');
        Route::post('/grades/{classSection}/components', [LecturerGradeController::class, 'saveComponents'])->name('grades.components');
        Route::post('/grades/{classSection}/publish', [LecturerGradeController::class, 'publish'])->name('grades.publish');
    });
