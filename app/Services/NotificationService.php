<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Vendor;
use App\Models\Booking;

class NotificationService
{
    public function createBookingReceivedNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user;
        $service = $booking->service;

        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_BOOKING_RECEIVED,
            'New Booking Received',
            "New booking received from {$client->name} for {$service->name}.",
            [
                'booking_id' => $booking->id,
                'client_name' => $client->name,
                'service_name' => $service->name,
                'event_date' => $booking->event->event_date ?? null
            ],
            'high',
            "/vendor/bookings/{$booking->id}"
        );
    }

    public function createBookingCompletedNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user;
        $service = $booking->service;

        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_BOOKING_COMPLETED,
            'Booking Completed',
            "Booking #{$booking->id} for {$client->name} has been completed successfully.",
            [
                'booking_id' => $booking->id,
                'client_name' => $client->name,
                'service_name' => $service->name,
                'amount' => $service->price
            ],
            'normal',
            "/vendor/bookings/{$booking->id}"
        );
    }

    public function createBookingConfirmedNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user;
        $service = $booking->service;

        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_BOOKING_CONFIRMED,
            'Booking Confirmed',
            "Booking #{$booking->id} for {$client->name} has been confirmed.",
            [
                'booking_id' => $booking->id,
                'client_name' => $client->name,
                'service_name' => $service->name,
                'event_date' => $booking->event->event_date ?? null
            ],
            'normal',
            "/vendor/bookings/{$booking->id}"
        );
    }

    public function createBookingCancelledNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user;
        $service = $booking->service;

        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_BOOKING_CANCELLED,
            'Booking Cancelled',
            "Booking #{$booking->id} for {$client->name} has been cancelled.",
            [
                'booking_id' => $booking->id,
                'client_name' => $client->name,
                'service_name' => $service->name
            ],
            'high',
            "/vendor/bookings/{$booking->id}"
        );
    }

    public function createServiceUpdatedNotification($service, $vendor)
    {
        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_SERVICE_UPDATED,
            'Service Updated',
            "Service \"{$service->name}\" has been updated successfully.",
            [
                'service_id' => $service->id,
                'service_name' => $service->name
            ],
            'low',
            "/vendor/services/{$service->id}/edit"
        );
    }

    public function createPaymentReceivedNotification($booking, $amount)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user;

        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_PAYMENT_RECEIVED,
            'Payment Received',
            "Payment of ₱{$amount} received from {$client->name} for booking #{$booking->id}.",
            [
                'booking_id' => $booking->id,
                'client_name' => $client->name,
                'amount' => $amount
            ],
            'normal',
            "/vendor/payments"
        );
    }

    public function createReviewReceivedNotification($review, $vendor)
    {
        $client = $review->user;
        $rating = $review->rating;

        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_REVIEW_RECEIVED,
            'New Review Received',
            "New {$rating}-star review received from {$client->name}.",
            [
                'review_id' => $review->id,
                'client_name' => $client->name,
                'rating' => $rating
            ],
            'normal',
            "/vendor/reviews"
        );
    }

    public function createSystemAlertNotification($vendor, $title, $message, $priority = 'normal')
    {
        return Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_SYSTEM_ALERT,
            $title,
            $message,
            null,
            $priority
        );
    }

    public function getNotificationsForVendor($vendorId, $limit = 10, $unreadOnly = false)
    {
        $query = Notification::forVendor($vendorId)
            ->orderBy('created_at', 'desc')
            ->take($limit);

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->get();
    }

    public function getUnreadCountForVendor($vendorId)
    {
        return Notification::forVendor($vendorId)->unread()->count();
    }

    public function markAllAsReadForVendor($vendorId)
    {
        return Notification::forVendor($vendorId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function deleteOldNotifications($days = 90)
    {
        return Notification::where('created_at', '<', now()->subDays($days))->delete();
    }
}
