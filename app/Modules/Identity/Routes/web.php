<?php

use App\Modules\Identity\Controllers\AuthController;
use App\Modules\Identity\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', [DashboardController::class, 'home'])->name('home');

    // Lop 1: role middleware. Lop 2 (Policy theo doi tuong) nam trong controller cua tung module.
    Route::get('/admin', [DashboardController::class, 'admin'])->middleware('role:admin')->name('admin.home');
    Route::get('/lecturer', [DashboardController::class, 'lecturer'])->middleware('role:lecturer')->name('lecturer.home');
    Route::get('/student', [DashboardController::class, 'student'])->middleware('role:student')->name('student.home');
});
