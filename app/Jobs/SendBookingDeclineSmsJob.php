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

class SendBookingDeclineSmsJob implements ShouldQueue
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

    public function handle(SemaphoreService $semaphoreService)
    {
        $booking = $this->booking->load(['user']);
        $user = $booking->user;

        if (!$user || !$user->contact_number) {
            \Log::warning("User contact number missing for declined booking ID: {$booking->id}");
            return;
        }

        $message = "Eventory: Hello {$user->name}, your booking request has been declined. Reason: {$this->reason}. Please check your Eventory account for more details.";

        try {
            $semaphoreService->send($user->contact_number, $message);
            \Log::info("Booking decline SMS sent successfully to user ID {$user->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking decline SMS for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e; // trigger retry
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingDeclineSmsJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}