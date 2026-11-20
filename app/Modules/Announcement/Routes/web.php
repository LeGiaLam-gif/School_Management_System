<?php

use App\Modules\Announcement\Controllers\LecturerAnnouncementController;
use App\Modules\Announcement\Controllers\StudentAnnouncementController;
use Illuminate\Support\Facades\Route;

/*
| Route web cua module Announcement (thong bao)
*/

Route::middleware(['auth', 'active', 'role:student'])->prefix('student/announcements')->name('student.announcements')->group(function () {
    Route::get('/', [StudentAnnouncementController::class, 'index']);
    Route::get('/{id}', [StudentAnnouncementController::class, 'show'])->name('.show');
});

Route::middleware(['auth', 'active', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get('/announcements', [LecturerAnnouncementController::class, 'index'])->name('announcements');
        Route::post('/announcements', [LecturerAnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [LecturerAnnouncementController::class, 'update'])->name('announcements.update');
        Route::patch('/announcements/{announcement}/pin', [LecturerAnnouncementController::class, 'togglePin'])->name('announcements.pin');
        Route::delete('/announcements/{announcement}', [LecturerAnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });
