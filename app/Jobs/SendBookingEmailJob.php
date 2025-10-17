<?php

namespace App\Jobs;

use App\Mail\Vendor\BookingRequestMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBookingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;

    // Retry up to 3 times if something goes wrong
    public $tries = 3;
    public $backoff = 10;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function handle()
    {
        $booking = $this->booking->load(['service.vendor.user']);

        $vendor = $booking->service->vendor;

        if (!$vendor || !$vendor->user) {
            \Log::warning("Vendor data missing for booking ID: {$booking->id}");
            return;
        }

        try {
            Mail::to($vendor->user->email)->send(new BookingRequestMail($booking));
            \Log::info("Booking email sent successfully to vendor ID {$vendor->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking email for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e; // triggers retry
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingEmailJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}