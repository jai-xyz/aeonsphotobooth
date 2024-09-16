<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\BackdropController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserPackageController;
use App\Http\Controllers\UserRegistrationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

    // USER ROUTES
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', UserMiddleware::class])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('user.profile.destroy');

    Route::get('/event', [UserRegistrationController::class, 'index'])->name('user.event.index');
    Route::get('/event/registration/{packageId?}', [UserRegistrationController::class, 'create'])->name('user.event.create');
    Route::post('/event/registration', [UserRegistrationController::class, 'store'])->name('user.event.store');
    Route::post('/event/registration', [UserRegistrationController::class, 'store'])->name('user.event.store');

    Route::get('/packages', [UserPackageController::class, 'index'])->name('user.package.index');
    
});


    // ADMIN ROUTES
Route::get('/admin', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified', AdminMiddleware::class])->name('admin.dashboard');

Route::middleware('auth', AdminMiddleware::class)->group(function () {
    Route::get('/admin/package', [PackageController::class, 'index'])->name('package.index');
    Route::get('/admin/package/add', [PackageController::class, 'create'])->name('package.create');
    Route::post('/admin/package/add', [PackageController::class, 'store'])->name('package.store');
    Route::get('/admin/package/edit/{package}', [PackageController::class, 'edit'])->name('package.edit');
    Route::patch('/admin/package/edit/{package}', [PackageController::class, 'update'])->name('package.update');

    Route::get('/admin/backdrop', [BackdropController::class, 'index'])->name('backdrop.index');
    Route::get('/admin/backdrop/add', [BackdropController::class, 'create'])->name('backdrop.create');
    Route::post('/admin/backdrop/addtype', [BackdropController::class, 'storeType'])->name('backdroptype.store');
    // Route::get('/admin/backdrop/color/add', [BackdropController::class, 'createColor'])->name('backdropcolor.create');
    Route::post('/admin/backdrop/addcolor', [BackdropController::class, 'storeColor'])->name('backdropcolor.store');
    
    Route::get('/admin/events', [RegistrationController::class, 'index'])->name('event.index');
    Route::patch('/admin/events/{event}', [RegistrationController::class, 'update'])->name('event.update');

});


        
require __DIR__.'/auth.php';
