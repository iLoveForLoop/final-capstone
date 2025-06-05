<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return inertia('Admin/Index');
    })->name('admin.index');

});

Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/vendor', function () {
        return inertia('Vendor/Index');
    })->name('vendor.index');

});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client', function () {
        return inertia('Client/Index');
    })->name('client.index');

});

Route::resource('service', ServiceController::class);

require __DIR__.'/auth.php';
