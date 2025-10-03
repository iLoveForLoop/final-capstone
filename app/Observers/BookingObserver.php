<?php
namespace App\Observers;

use App\Mail\Vendor\BookingRequestMail;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;

class BookingObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;

    }

    public function created(Booking $booking)
    {
        $booking->load(['service.vendor.user', 'user']);

        $this->notificationService->createBookingReceivedNotification($booking);

        Mail::to($booking->service->vendor->user->email)->send(new BookingRequestMail($booking));

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