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
    ]);
})->middleware(['auth', UserMiddleware::class])->name('home');

// USER ROUTES
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', UserMiddleware::class])->name('dashboard');

Route::middleware('auth', UserMiddleware::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('user.profile.destroy');

    Route::get('/event', [UserRegistrationController::class, 'index'])->name('user.event.index');
    Route::get('/event/registration/{packageId?}', [UserRegistrationController::class, 'create'])->name('user.event.create');
    Route::post('/event/registration', [UserRegistrationController::class, 'store'])->name('user.event.store');
    Route::get('/event', [UserRegistrationController::class, 'index'])->name('user.event.index');
    Route::get('/event/registration-confirmation', [UserRegistrationController::class, 'indexConfirmation'])->name('user.event.confirmation');

    Route::get('/packages', [UserPackageController::class, 'index'])->name('user.package.index');
    
});

// ADMIN ROUTES
Route::get('/admin', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified', AdminMiddleware::class])->name('admin.dashboard');

Route::middleware('auth', AdminMiddleware::class)->group(function () {
    // PACKAGES ROUTES
    Route::get('/admin/package', [PackageController::class, 'index'])->name('package.index');
    Route::get('/admin/package/add', [PackageController::class, 'create'])->name('package.create');
    Route::post('/admin/package/add', [PackageController::class, 'store'])->name('package.store');
    Route::patch('/admin/package/edit/{package}', [PackageController::class, 'update'])->name('package.update');
    Route::delete('/admin/package/delete/{package}', [PackageController::class, 'destroy'])->name('package.destroy');

    // BACKDROP ROUTES
    Route::get('/admin/backdrop/list', [BackdropController::class, 'index'])->name('backdrop.index');
    Route::post('/admin/backdrop/list', [BackdropController::class, 'storeBackdrop'])->name('backdrop.store');
    Route::post('/admin/backdrop/list/edit/{backdrop}', [BackdropController::class, 'updateBackdrop'])->name('backdrop.update');
    Route::delete('/admin/backdrop/type/delete/{backdrop}', [BackdropController::class, 'destroyBackdrop'])->name('backdrop.destroy');

    // BACKDROP TYPE ROUTES
    Route::get('/admin/backdrop/type', [BackdropController::class, 'indexType'])->name('backdroptype.index');
    Route::post('/admin/backdrop/type', [BackdropController::class, 'storeType'])->name('backdroptype.store');
    Route::patch('/admin/backdrop/type/edit/{backdroptype}', [BackdropController::class, 'updateBackdropType'])->name('backdroptype.update');
    Route::delete('/admin/backdrop/type/delete/{backdroptype}', [BackdropController::class, 'destroyBackdropType'])->name('backdroptype.destroy');

    // EVENTS PENDING ROUTES
    Route::get('/admin/pending-events', [RegistrationController::class, 'indexPending'])->name('event.pending.index');
    Route::patch('/admin/pending-events/{event}', [RegistrationController::class, 'update'])->name('event.update');

    // EVENTS ACCEPTED ROUTES
    Route::get('/admin/accepted-events', [RegistrationController::class, 'indexAccepted'])->name('event.accepted.index');

    // EVENTS LISTED ROUTES
    Route::get('/admin/listed-events', [RegistrationController::class, 'index'])->name('event.index');


    // Route::get('/admin/pay', [RegistrationController::class, 'pay'])->name('event.pay');
});



require __DIR__ . '/auth.php';
