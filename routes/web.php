<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CateringServiceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PhotographyServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorCalendarController;
use App\Http\Controllers\VendorApplicationController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WelcomeController;
use App\Models\Service;
use App\Models\Vendor;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [WelcomeController::class, 'index']);

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

    //calendar
    Route::get('/calendar', [VendorCalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/bookings/month', [VendorCalendarController::class, 'getBookingsForMonth'])->name('calendar.bookings.month');
    Route::get('/calendar/bookings/day', [VendorCalendarController::class, 'getBookingsForDay'])->name('calendar.bookings.day');

    //catering service page
    Route::resource('catering-services', CateringServiceController::class);

    //Photography
    Route::resource('photography-services', PhotographyServiceController::class);

    //notifications
    Route::post('/notifications/{notification}/read', [VendorController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [VendorController::class, 'markAllNotificationsAsRead'])->name('notifications.readAll');
    Route::get('/notifications', [VendorController::class, 'getNotificationsList'])->name('notifications.list');

    //bookings
    // Route::resource('bookings', BookingController::class);

    // Booking Management Routes
    Route::controller(BookingController::class)->prefix('bookings')->name('bookings.')->group(function () {

        // Main booking index page with filtering
        Route::get('/', 'index')->name('index');

        // View specific booking details
        Route::get('/{id}', 'show')->name('show');

        // Booking status actions
        Route::patch('/{id}/accept', 'accept')->name('accept');
        Route::patch('/{id}/decline', 'decline')->name('decline'); // This cancels the booking
        Route::patch('/{id}/complete', 'complete')->name('complete');

        // Bulk actions
        Route::patch('/bulk/update', 'bulkUpdate')->name('bulk.update');

        // Export functionality
        Route::get('/export/csv', 'export')->name('export');

    });



});







Route::prefix('client')->as('client.')->middleware(['auth', 'role:client'])->group(function () {

    Route::get('/', [ClientController::class, 'index'])->name('index');

    //Adding Booking
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

});



//API CALLS
Route::middleware('auth')->get('/api/vendor-bookings-notifications', [NotificationController::class, 'getVendorUnreadBookingNotifications']);


require __DIR__.'/auth.php';
