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

class SendBookingSmsJob implements ShouldQueue
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
        $booking = $this->booking->load(['service.vendor.user', 'user']);
        $vendor = $booking->service->vendor;

        if (!$vendor || !$vendor->contact_number) {
            \Log::warning("Vendor contact missing for booking ID: {$booking->id}");
            return;
        }

        $message = "Eventory: Hello {$booking->service->vendor->business_name}, your service {$booking->service->name} was booked. View the booking details on your Eventory account.";

        try {
            $semaphoreService->send($vendor->contact_number, $message);
            \Log::info("Booking SMS sent successfully to vendor {$vendor->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking SMS for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e; // trigger retry
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingSmsJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}
