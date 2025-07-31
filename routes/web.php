<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CateringServiceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\PhotographyServiceController;
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

    $services = Service::paginate(5);

    $services->getCollection()->transform(function ($service) {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description,
            'price' => $service->price,
            'image_url' => $service->getFirstMediaUrl('images')
        ];
    });

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'services' => $services
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

    //users page
    Route::resource('users', UserController::class);

    //vendor applications page
    Route::resource('vendor-application', VendorApplicationController::class);
    Route::put('vendor-application/{vendor}/approve', [VendorApplicationController::class, 'approve'])->name('vendor-application.approve');
    Route::delete('vendor-application/{vendor}/reject', [VendorApplicationController::class, 'reject'])->name('vendor-application.reject');

    //categories page
    Route::resource('categories', ServiceCategoryController::class);

    //services page
    Route::get('/services', [AdminController::class, 'servicesPage'])->name('services.index');
    Route::post('/services', [AdminController::class, 'addService'])->name('services.store');
    Route::resource('service', ServiceController::class);

    //wapa nahuman page
    Route::get('/bookings', [AdminController::class, 'bookingsPage'])->name('bookings.index');
    Route::get('/reviews', [AdminController::class, 'reviewsPage'])->name('reviews.index');
    Route::get('/payments', [AdminController::class, 'paymentsPage'])->name('payments.index');
    Route::get('/settings', [AdminController::class, 'settingsPage'])->name('settings.index');

});







Route::prefix('vendor')->as('vendor.')->middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/', [VendorController::class, 'index'])->name('index');

    //service page
    Route::resource('services', ServiceController::class);

    //dishes page
    Route::resource('dishes', DishController::class);
    Route::patch('dishes/{dish}/toggle-availability', [DishController::class, 'toggleAvailability'])
        ->name('dishes.toggle-availability');

    //catering service page
    Route::resource('catering-services', CateringServiceController::class);

    //Photography
    Route::resource('photography-services', PhotographyServiceController::class);

});







Route::prefix('client')->as('client.')->middleware(['auth', 'role:client'])->group(function () {

    Route::get('/', [ClientController::class, 'index'])->name('index');

});



require __DIR__.'/auth.php';
