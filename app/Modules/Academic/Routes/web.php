<?php

use App\Modules\Academic\Controllers\StudentCurriculumController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Academic
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/curriculum', [StudentCurriculumController::class, 'curriculum'])->name('curriculum');
    Route::get('/courses', [StudentCurriculumController::class, 'courses'])->name('courses');
    Route::get('/courses/{code}', [StudentCurriculumController::class, 'courseDetail'])->name('courses.detail');
});
