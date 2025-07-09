<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorApplicationController;
use App\Http\Controllers\VendorController;
use App\Models\Service;
use App\Models\Vendor;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->as('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::resource('user', UserController::class);
    Route::resource('vendor-application', VendorApplicationController::class);
    Route::put('vendor-application/{vendor}/approve', [VendorApplicationController::class, 'approve'])->name('vendor-application.approve');
    Route::delete('vendor-application/{vendor}/reject', [VendorApplicationController::class, 'reject'])->name('vendor-application.reject');
    Route::resource('categories', ServiceCategoryController::class);

    Route::get('/services', [AdminController::class, 'servicesPage'])->name('services.index');

});

Route::prefix('vendor')->middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('vendor.index');
    Route::resource('service', ServiceController::class);

});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client', function () {
        return inertia('Client/Index');
    })->name('client.index');

});



require __DIR__.'/auth.php';