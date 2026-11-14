<?php

use App\Modules\Grading\Controllers\StudentGradeController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Grading (bang diem, GPA)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades');
});
