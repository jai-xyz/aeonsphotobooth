<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

    // USER ROUTES
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/event', [RegistrationController::class, 'index'])->name('event.index');
    Route::get('/event/registration', [RegistrationController::class, 'create'])->name('event.create');
    Route::post('/event/registration', [RegistrationController::class, 'store'])->name('event.store');
});


    // ADMIN ROUTES
Route::get('/admin', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified'])->name('admin.dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/admin/package', [PackageController::class, 'index'])->name('package.index');
    Route::get('/admin/package/add', [PackageController::class, 'create'])->name('package.create');
    Route::post('/admin/package/add', [PackageController::class, 'store'])->name('package.store');
    Route::get('/admin/package/edit/{package}', [PackageController::class, 'edit'])->name('package.edit');
    Route::patch('/admin/package/edit/{package}', [PackageController::class, 'update'])->name('package.update');
});


        
require __DIR__.'/auth.php';
