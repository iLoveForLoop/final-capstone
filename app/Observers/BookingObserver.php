<?php
namespace App\Observers;

use App\Jobs\SendBookingCompletedEmailJob;
use App\Jobs\SendBookingCompletedSmsJob;
use App\Jobs\SendBookingConfirmedEmailJob;
use App\Jobs\SendBookingConfirmedSmsJob;
use App\Jobs\SendBookingEmailJob;
use App\Jobs\SendBookingSmsJob;
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

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(Booking $booking)
    {
        $booking->load(['service.vendor.user', 'user.client']);

        // Create internal system notifications first
        $this->notificationService->createBookingReceivedNotification($booking);
        $this->notificationService->createBookingSubmittedClientNotification($booking);

        // Chain jobs: Email first, then SMS
        SendBookingEmailJob::dispatch($booking)->chain([
            new SendBookingSmsJob($booking),
        ]);

        // SendBookingSmsJob::dispatch($booking);
    }

    public function updated(Booking $booking)
    {
        $booking->load(['service.vendor.user', 'user.client']);

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

                        // Mail::to($booking->user->email)->queue(new BookingConfirmedMail($booking));
                        // dd($booking->user->client->contact_number);
                        SendBookingConfirmedEmailJob::dispatch($booking)->chain([
                            new SendBookingConfirmedSmsJob($booking)
                        ]);
                        // SendBookingConfirmedEmailJob::dispatch($booking);
                    }
                    break;

                case 'completed':
                    if ($oldStatus !== 'completed') {
                        // Vendor notification
                        $this->notificationService->createBookingCompletedNotification($booking);

                        // Client notification
                        $this->notificationService->createBookingCompletedClientNotification($booking);

                        // Optional: Send completion email to client
                        // Mail::to($booking->user->email)->queue(new BookingCompletedMail($booking));
                        SendBookingCompletedEmailJob::dispatch($booking)->chain([
                            new SendBookingCompletedSmsJob($booking)
                        ]);
                        // SendBookingCompletedSmsJob::dispatch($booking);
                        // SendBookingCompletedEmailJob::dispatch($booking);
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
            case 'declined':
                    if ($oldStatus !== 'declined') {
                        // Vendor notification - existing
                        $this->notificationService->createBookingDeclinedNotification($booking);

                        // Client notification - NEW
                        $this->notificationService->createBookingDeclinedClientNotification($booking);

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
