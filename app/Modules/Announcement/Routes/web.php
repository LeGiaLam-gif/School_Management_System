<?php

use App\Modules\Announcement\Controllers\LecturerAnnouncementController;
use Illuminate\Support\Facades\Route;

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
