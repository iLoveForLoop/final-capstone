<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\SemaphoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendBookingConfirmedSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function handle(SemaphoreService $semaphoreService)
    {
        $booking = $this->booking;
        $vendor = $booking->service->vendor;
        $client = $booking->user->client;

        if (!$client || !$client->contact_number) {
            \Log::warning("Client contact missing for booking ID: {$booking->id}");
            return;
        }

        $message = "Eventory: Your booking with {$vendor->business_name} has been confirmed. Please check your Eventory account for the booking details.";

        try {
            $semaphoreService->send($client->contact_number, $message);
            \Log::info("Booking confirmed SMS sent successfully to client ID {$client->contact_number}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking confirmed SMS for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e; // triggers retry
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingConfirmedSmsJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}
