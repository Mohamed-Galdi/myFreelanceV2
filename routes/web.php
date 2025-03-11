<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WorkController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// Login Routes...

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store']);
});



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Client Routes
    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('client');

    // Project Routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('project');

    // Work Routes
    Route::get('/works', [WorkController::class, 'index'])->name('works');
    Route::get('/works/{work}', [WorkController::class, 'show'])->name('work');

    // Payment Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payment');

    // Settings Routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

    // Logout Route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
});
