<?php

use App\Modules\Academic\Controllers\Admin\AdministrativeClassController;
use App\Modules\Academic\Controllers\Admin\DepartmentController;
use App\Modules\Academic\Controllers\Admin\ProgramController;
use App\Modules\Academic\Controllers\StudentCurriculumController;
use Illuminate\Support\Facades\Route;

/*
| Route web của module Academic
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/curriculum', [StudentCurriculumController::class, 'curriculum'])->name('curriculum');
    Route::get('/courses', [StudentCurriculumController::class, 'courses'])->name('courses');
    Route::get('/courses/{code}', [StudentCurriculumController::class, 'courseDetail'])->name('courses.detail');
});

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('administrative-classes', AdministrativeClassController::class);
});
