<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

// ── Public ──────────────────────────────────────────────────
Route::get('/', [AuthController::class, 'landing'])->name('landing');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Citizen pages ───────────────────────────────────────────
Route::middleware(EnsureRole::class . ':citizen')->group(function () {
    Route::get('/dashboard', [GrievanceController::class, 'dashboard'])->name('citizen.dashboard');
    Route::get('/submit', [GrievanceController::class, 'create'])->name('citizen.submit');
    Route::post('/grievances', [GrievanceController::class, 'store'])->name('grievances.store');
    Route::get('/track', [GrievanceController::class, 'track'])->name('citizen.track');
});

// ── Admin pages ─────────────────────────────────────────────
Route::middleware(EnsureRole::class . ':admin')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::patch('/grievances/{grievance}', [AdminController::class, 'update'])->name('admin.grievances.update');
});

// ── JSON endpoints used by the JS (citizen OR admin) ────────
Route::prefix('api')->middleware('auth:web')->group(function () {
    Route::get('/grievances', [GrievanceController::class, 'index']);
    Route::get('/grievances/{id}', [GrievanceController::class, 'show']);
    Route::get('/grievances/{id}/photo', [GrievanceController::class, 'photo'])->whereNumber('id');
    Route::delete('/grievances/{grievance}', [GrievanceController::class, 'destroy']);
    Route::get('/grievances/{grievance}/messages', [MessageController::class, 'index']);
    Route::post('/grievances/{grievance}/messages', [MessageController::class, 'store']);
});
