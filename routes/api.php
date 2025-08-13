<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;

// API routes are automatically prefixed with '/api'
Route::get('/services/{category}', [ServiceController::class, 'getByCategory']);
Route::get('/vendor-bookings-notifications', [NotificationController::class, 'getVendorUnreadBookingNotifications']);
