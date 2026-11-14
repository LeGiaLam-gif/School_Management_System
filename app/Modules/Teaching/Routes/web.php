<?php

use App\Modules\Teaching\Controllers\StudentTimetableController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Teaching (lop hoc phan, thoi khoa bieu)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/timetable', [StudentTimetableController::class, 'index'])->name('timetable');
});
