<?php

namespace App\Jobs;

use App\Mail\Customer\BookingCancelledMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBookingCancellationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;
    public $reason;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(Booking $booking, string $reason)
    {
        $this->booking = $booking;
        $this->reason = $reason;
    }

    public function handle()
    {
        $booking = $this->booking->load(['user']);
        $user = $booking->user;

        if (!$user || !$user->email) {
            \Log::warning("User email missing for cancelled booking ID: {$booking->id}");
            return;
        }

        try {
            Mail::to($user->email)->send(new BookingCancelledMail($booking, $this->reason));
            \Log::info("Booking cancellation email sent successfully to user ID {$user->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking cancellation email for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingCancellationEmailJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}