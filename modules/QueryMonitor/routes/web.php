<?php

use Illuminate\Support\Facades\Route;
use Modules\QueryMonitor\Http\Controllers\AuthController;
use Modules\QueryMonitor\Http\Controllers\DashboardController;
use Modules\QueryMonitor\Http\Controllers\TuningController;
use Modules\QueryMonitor\Http\Controllers\UserController;

// Auth routes (riêng biệt với hệ thống chính)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/setup', [AuthController::class, 'setup'])->name('setup');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (chỉ tài khoản của Query Monitor mới vào được)
Route::middleware(['query-monitor.auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/live', [DashboardController::class, 'live'])->name('live');
    Route::get('/api/live', [DashboardController::class, 'liveData'])->name('api.live');
    Route::get('/api/query/{id}', [DashboardController::class, 'queryDetail'])->name('api.query');
    Route::get('/tuning', [TuningController::class, 'index'])->name('tuning');
    Route::get('/tuning/page', [TuningController::class, 'pageDetail'])->name('tuning.page');
    Route::post('/tuning/benchmark', [TuningController::class, 'benchmark'])->name('tuning.benchmark');
    Route::post('/tuning/note', [TuningController::class, 'saveNote'])->name('tuning.note');
    Route::get('/queries', [DashboardController::class, 'queries'])->name('queries');
    Route::get('/fingerprint/{hash}', [DashboardController::class, 'fingerprint'])->name('fingerprint');
    Route::post('/explain/{id}', [DashboardController::class, 'explain'])->name('explain');
    Route::get('/tables', [DashboardController::class, 'tables'])->name('tables');

    // Admin-only routes (quản lý cấu hình & tài khoản)
    Route::middleware(['query-monitor.auth:admin'])->group(function () {
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/purge', [DashboardController::class, 'purge'])->name('settings.purge');

        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
        Route::post('/users/{id}/password', [UserController::class, 'resetPassword'])->name('users.password');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
