<?php

use App\Modules\Identity\Controllers\AuthController;
use App\Modules\Identity\Controllers\DashboardController;
use App\Modules\Identity\Controllers\ProfileController;
use App\Modules\Identity\Controllers\StudentProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', [DashboardController::class, 'home'])->name('home');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/contact', [ProfileController::class, 'updateContact'])->name('profile.contact');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Lop 1: role middleware. Lop 2 (Policy theo doi tuong) nam trong controller cua tung module.
    Route::get('/admin', [DashboardController::class, 'admin'])->middleware('role:admin')->name('admin.home');
    Route::get('/lecturer', [DashboardController::class, 'lecturer'])->middleware('role:lecturer')->name('lecturer.home');
    Route::get('/student', [DashboardController::class, 'student'])->middleware('role:student')->name('student.home');

    // Cổng sinh viên - Hồ sơ & tài khoản cá nhân
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');
        Route::put('/profile/contact', [StudentProfileController::class, 'updateContact'])->name('profile.contact');
    });
});
