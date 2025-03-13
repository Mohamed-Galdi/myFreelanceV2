<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TempFileController;
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
    Route::post('/clients', [ClientController::class, 'store'])->name('admin.clients.store');
    Route::post('/clients/{client}', [ClientController::class, 'update'])->name('admin.clients.update');
    Route::post('/clients/{client}/delete', [ClientController::class, 'destroy'])->name('admin.clients.delete');

    // Project Routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('project');
    Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::post('/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');

    // <------------> TODO: delete related files
    Route::post('/projects/{project}/delete', [ProjectController::class, 'destroy'])->name('admin.projects.delete');

    // Work Routes
    Route::get('/works', [WorkController::class, 'index'])->name('works');
    Route::get('/works/{work}', [WorkController::class, 'show'])->name('work');
    Route::post('/works', [WorkController::class, 'store'])->name('admin.works.store');
    Route::post('/works/{work}', [WorkController::class, 'update'])->name('admin.works.update');
    Route::post('/works/{work}/delete', [WorkController::class, 'destroy'])->name('admin.works.delete');

    // Payment Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payment');

    // Settings Routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

    // file upload
    Route::post('/upload', [TempFileController::class, 'upload'])->name('file.upload');
    Route::post('/revert/{id}', [TempFileController::class, 'revert'])->name('file.revert');

    // Logout Route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
});


