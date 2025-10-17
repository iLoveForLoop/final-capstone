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

class SendBookingCompletedSmsJob implements ShouldQueue
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
            \Log::warning("Vendor contact missing for booking ID: {$booking->id}");
            return;
        }

        $message = "Eventory: Hi {$client->full_name}, your booking with {$vendor->business_name} is now completed. We’d love to hear your feedback! Rate your experience in your Eventory account.
";

        try {
            $semaphoreService->send($client->contact_number, $message);
            \Log::info("Booking completed SMS sent successfully to vendor ID {$client->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking completed SMS for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingCompletedSmsJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}