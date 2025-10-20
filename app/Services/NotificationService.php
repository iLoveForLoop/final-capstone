<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Vendor;
use App\Models\Booking;
use App\Events\NotificationCreated;

class NotificationService
{
    public function createBookingReceivedNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user->client;
        $service = $booking->service;

        $notification = Notification::createForVendor(
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

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createBookingCompletedNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user->client;
        $service = $booking->service;

        $notification = Notification::createForVendor(
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

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createBookingConfirmedNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user->client;
        $service = $booking->service;

        $notification = Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_BOOKING_CONFIRMED,
            'Booking Confirmed',
            "Booking #{$booking->id} for {$client->full_name} has been confirmed.",
            [
                'booking_id' => $booking->id,
                'client_name' => $client->name,
                'service_name' => $service->name,
                'event_date' => $booking->event->event_date ?? null
            ],
            'normal',
            "/vendor/bookings/{$booking->id}"
        );

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createBookingCancelledNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user->client;
        $service = $booking->service;

        $notification = Notification::createForVendor(
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

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createBookingDeclinedNotification($booking)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user->client;
        $service = $booking->service;

        $notification = Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_BOOKING_DECLINED,
            'Booking Declined',
            "Booking #{$booking->id} for {$client->name} has been declined.",
            [
                'booking_id' => $booking->id,
                'client_name' => $client->name,
                'service_name' => $service->name
            ],
            'high',
            "/vendor/bookings/{$booking->id}"
        );

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createServiceUpdatedNotification($service, $vendor)
    {
        $notification = Notification::createForVendor(
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

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createPaymentReceivedNotification($booking, $amount)
    {
        $vendor = $booking->service->vendor;
        $client = $booking->user->client;

        $notification = Notification::createForVendor(
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

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createReviewReceivedNotification($review, $vendor)
    {
        $client = $review->user;
        $rating = $review->rating;

        $notification = Notification::createForVendor(
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

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function createSystemAlertNotification($vendor, $title, $message, $priority = 'normal')
    {
        $notification = Notification::createForVendor(
            $vendor->id,
            Notification::TYPE_SYSTEM_ALERT,
            $title,
            $message,
            null,
            $priority
        );

        // Broadcast the notification
        broadcast(new NotificationCreated($notification));

        return $notification;
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

    public function getUnreadBookingCountForVendor($vendorId){
        return Notification::forVendor($vendorId)
        ->unread()
        ->ofType(Notification::TYPE_BOOKING_RECEIVED)
        ->count();
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


    // CLIENT NOTIFICATIONS METHOD
    public function createBookingConfirmedClientNotification($booking)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;
        $service = $booking->service;

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_BOOKING_CONFIRMED_CLIENT,
            'Booking Confirmed! 🎉',
            "Your booking for {$service->name} with {$vendor->business_name} has been confirmed.",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'service_name' => $service->name,
                'event_date' => $booking->event->event_date ?? null,
                'vendor_id' => $vendor->id
            ],
            'normal',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    public function createBookingUpdatedClientNotification($booking)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;
        $service = $booking->service;

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_BOOKING_UPDATED,
            'Booking Updated',
            "Your booking for {$service->name} has been updated.",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'service_name' => $service->name,
                'vendor_id' => $vendor->id
            ],
            'normal',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    public function createPaymentConfirmedClientNotification($booking, $amount)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_PAYMENT_CONFIRMED,
            'Payment Confirmed ✅',
            "Your payment of ₱{$amount} for booking #{$booking->id} has been confirmed.",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'amount' => $amount,
                'vendor_id' => $vendor->id
            ],
            'normal',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    public function createBookingCompletedClientNotification($booking)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;
        $service = $booking->service;

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_BOOKING_COMPLETED_CLIENT,
            'Service Completed!',
            "Your booking for {$service->name} has been completed. Thank you for your business!",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'service_name' => $service->name,
                'vendor_id' => $vendor->id
            ],
            'normal',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    public function createVendorMessageNotification($clientId, $vendor, $message)
    {
        $notification = Notification::createForUser(
            $clientId,
            Notification::TYPE_VENDOR_MESSAGE,
            "Message from {$vendor->business_name}",
            $message,
            [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->business_name
            ],
            'normal',
            "/client/messages?vendor_id={$vendor->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    // Client-specific query methods
    public function getNotificationsForClient($clientId, $limit = 10, $unreadOnly = false)
    {
        $query = Notification::forUser($clientId)
            ->orderBy('created_at', 'desc')
            ->take($limit);

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->get();
    }

    public function getUnreadCountForClient($clientId)
    {
        return Notification::forUser($clientId)->unread()->count();
    }

    public function markAllAsReadForClient($clientId)
    {
        return Notification::forUser($clientId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    // Add these methods to your existing NotificationService class

    public function createBookingSubmittedClientNotification($booking)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;
        $service = $booking->service;

        // dd($client);

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_BOOKING_SUBMITTED,
            'Booking Submitted Successfully!',
            "Your booking request for {$service->name} with {$vendor->business_name} has been submitted and is pending confirmation.",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'service_name' => $service->name,
                'vendor_id' => $vendor->id,
                'event_date' => $booking->event->event_date ?? null
            ],
            'normal',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    public function createBookingInProgressClientNotification($booking)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;
        $service = $booking->service;

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_BOOKING_IN_PROGRESS,
            'Service Started!',
            "{$vendor->business_name} has started working on your booking for {$service->name}.",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'service_name' => $service->name,
                'vendor_id' => $vendor->id
            ],
            'normal',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    public function createBookingCancelledClientNotification($booking)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;
        $service = $booking->service;

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_BOOKING_CANCELLED_CLIENT,
            'Booking Cancelled',
            "Your booking for {$service->name} with {$vendor->business_name} has been cancelled.",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'service_name' => $service->name,
                'vendor_id' => $vendor->id,
                'cancellation_reason' => $booking->cancellation_reason ?? null
            ],
            'high',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }

    public function createBookingDeclinedClientNotification($booking)
    {
        $client = $booking->user->client;
        $vendor = $booking->service->vendor;
        $service = $booking->service;

        $notification = Notification::createForUser(
            $client->id,
            Notification::TYPE_BOOKING_DECLINED_CLIENT,
            'Booking Declined',
            "Your booking for {$service->name} with {$vendor->business_name} has been declined.",
            [
                'booking_id' => $booking->id,
                'vendor_name' => $vendor->business_name,
                'service_name' => $service->name,
                'vendor_id' => $vendor->id,
                'cancellation_reason' => $booking->cancellation_reason ?? null
            ],
            'normal',
            "/client/bookings/{$booking->id}"
        );

        broadcast(new NotificationCreated($notification));
        return $notification;
    }



}
