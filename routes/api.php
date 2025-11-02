<?php

use App\Http\Controllers\AIChatController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Models\ServiceCategory;

// API routes are automatically prefixed with '/api'
Route::get('/services/{category}', [ServiceController::class, 'getByCategory']);
// Route::get('/vendor-bookings-notifications', [NotificationController::class, 'getVendorUnreadBookingNotifications']);


// AI
Route::post('/ai/message', [AIChatController::class, 'sendMessage'])
    ->middleware('throttle:20,1');

Route::delete('/chat/clear', [AIChatController::class, 'clearConversation'])
    ->middleware('throttle:10,1');



Route::patch('/vendor/{service}/toggle-availability', [ServiceController::class, 'toggleAvailability'])->name('vendor.services.toggle-availability');

Route::get('/vendor/{vendor}/booked-dates', [BookingController::class, 'getBookedDates']);

// Route::get('/vendor/{id}/booked-times', function ($id) {
//     $date = request('date');

//     $booked = [
//         '2025-10-15' => [9, 14],
//         '2025-10-20' => [10, 11, 15],
//     ];

//     return response()->json([
//         'bookedTimes' => $booked[$date] ?? [],
//     ]);
// });

Route::middleware(['auth', 'vendor'])->group(function (){
    Route::get('/categories/all', [ServiceCategory::class, 'getCategories']);
});
