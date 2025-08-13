<?php
namespace App\Observers;

use App\Models\Booking;
use App\Services\NotificationService;

class BookingObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(Booking $booking)
    {
        $this->notificationService->createBookingReceivedNotification($booking);
    }

    public function updated(Booking $booking)
    {
        if ($booking->wasChanged('status')) {
            $oldStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;

            switch ($newStatus) {
                case 'confirmed':
                    if ($oldStatus === 'pending') {
                        $this->notificationService->createBookingConfirmedNotification($booking);
                    }
                    break;
                case 'completed':
                    if ($oldStatus !== 'completed') {
                        $this->notificationService->createBookingCompletedNotification($booking);
                    }
                    break;
                case 'cancelled':
                    if ($oldStatus !== 'cancelled') {
                        $this->notificationService->createBookingCancelledNotification($booking);
                    }
                    break;
            }
        }
    }
}
