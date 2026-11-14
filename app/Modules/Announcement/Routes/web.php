<?php

use App\Modules\Announcement\Controllers\StudentAnnouncementController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Announcement (thong bao)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student/announcements')->name('student.announcements')->group(function () {
    Route::get('/', [StudentAnnouncementController::class, 'index']);
    Route::get('/{id}', [StudentAnnouncementController::class, 'show'])->name('.show');
});
