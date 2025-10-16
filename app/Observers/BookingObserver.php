<?php
namespace App\Observers;

use App\Mail\Customer\BookingConfirmedMail;
use App\Mail\Vendor\BookingRequestMail;
use App\Mail\Customer\BookingCompletedMail;
use App\Mail\Customer\BookingCancelledMail;
use App\Models\Booking;
use App\Services\NotificationService;
use App\Services\SemaphoreService;
use Illuminate\Support\Facades\Mail;

class BookingObserver
{
    protected $notificationService;
    protected $semaphoreService;

    public function __construct(NotificationService $notificationService, SemaphoreService $semaphoreService)
    {
        $this->notificationService = $notificationService;
        $this->semaphoreService = $semaphoreService;
    }

    public function created(Booking $booking)
    {
        $booking->load(['service.vendor.user', 'user.client']);

        // Vendor notification - existing
        $this->notificationService->createBookingReceivedNotification($booking);

        // Client notification for booking submitted
        $this->notificationService->createBookingSubmittedClientNotification($booking);

        Mail::to($booking->service->vendor->user->email)->queue(new BookingRequestMail($booking));
        $this->semaphoreService->send($booking->service->vendor->contact_number, `Eventory: Hi {$booking->service->vendor->business_name}, your service has been booked by a client. View the booking details on your Eventory account.`);

    }

    public function updated(Booking $booking)
    {
        $booking->load(['service.vendor.user', 'user']);

        if ($booking->wasChanged('status')) {
            $oldStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;

            switch ($newStatus) {
                case 'confirmed':
                    if ($oldStatus === 'pending') {
                        // Vendor notification - existing
                        $this->notificationService->createBookingConfirmedNotification($booking);

                        // Client notification - NEW
                        $this->notificationService->createBookingConfirmedClientNotification($booking);

                        Mail::to($booking->user->email)->queue(new BookingConfirmedMail($booking));
                    }
                    break;

                case 'completed':
                    if ($oldStatus !== 'completed') {
                        // Vendor notification - existing
                        $this->notificationService->createBookingCompletedNotification($booking);

                        // Client notification - NEW
                        $this->notificationService->createBookingCompletedClientNotification($booking);

                        // Optional: Send completion email to client
                        Mail::to($booking->user->email)->queue(new BookingCompletedMail($booking));
                    }
                    break;

                case 'cancelled':
                    if ($oldStatus !== 'cancelled') {
                        // Vendor notification - existing
                        $this->notificationService->createBookingCancelledNotification($booking);

                        // Client notification - NEW
                        $this->notificationService->createBookingCancelledClientNotification($booking);

                        // Optional: Send cancellation email to client
                        // Mail::to($booking->user->email)->queue(new BookingCancelledMail($booking));
                    }
                    break;

                case 'in_progress':
                    if ($oldStatus !== 'in_progress') {
                        // NEW: Notify client when vendor starts the service
                        $this->notificationService->createBookingInProgressClientNotification($booking);
                    }
                    break;
            }
        }

        // Notify client about other important booking updates
        if ($booking->wasChanged(['event_date', 'event_location', 'special_requests'])) {
            if (!in_array($booking->status, ['cancelled', 'completed'])) {
                $this->notificationService->createBookingUpdatedClientNotification($booking);
            }
        }

        // Notify client when payment is confirmed
        if ($booking->wasChanged('payment_status') && $booking->payment_status === 'paid') {
            $this->notificationService->createPaymentConfirmedClientNotification($booking, $booking->total_amount);
        }
    }

    // You can also add a deleting method if you want to notify about booking deletion
    public function deleting(Booking $booking)
    {
        // Optional: Notify client if booking is being deleted
        // $this->notificationService->createBookingDeletedClientNotification($booking);
    }
}