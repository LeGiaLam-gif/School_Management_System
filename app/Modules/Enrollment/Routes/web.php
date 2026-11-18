<?php

use App\Modules\Enrollment\Controllers\StudentEnrollmentController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Enrollment (dang ky/huy hoc phan)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student/enrollment')->name('student.enrollment')->group(function () {
    Route::get('/', [StudentEnrollmentController::class, 'index']);
    Route::post('/register', [StudentEnrollmentController::class, 'register'])->name('.register');
    Route::post('/drop', [StudentEnrollmentController::class, 'drop'])->name('.drop');
});
