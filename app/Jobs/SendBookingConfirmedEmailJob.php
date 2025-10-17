<?php

namespace App\Jobs;

use App\Mail\Customer\BookingConfirmedMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBookingConfirmedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function handle()
    {
        $booking = $this->booking;
        $client = $booking->user;

        if (!$client || !$client->email) {
            \Log::warning("Client email missing for booking ID: {$booking->id}");
            return;
        }

        try {
            Mail::to($client->email)->send(new BookingConfirmedMail($booking));
            \Log::info("Booking confirmed email sent successfully to client ID {$client->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking confirmed email for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e; // triggers retry
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingConfirmedEmailJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}
