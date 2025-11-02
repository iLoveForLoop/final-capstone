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

class SendBookingCancellationSmsJob implements ShouldQueue
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
            \Log::warning("User contact number missing for cancelled booking ID: {$booking->id}");
            return;
        }

        $message = "Eventory: Hello {$user->name}, your booking has been cancelled. Reason: {$this->reason}. For more details, please check your Eventory account.";

        try {
            $semaphoreService->send($user->contact_number, $message);
            \Log::info("Booking cancellation SMS sent successfully to user ID {$user->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send booking cancellation SMS for booking ID {$booking->id}: {$e->getMessage()}");
            throw $e; // trigger retry
        }
    }

    public function failed(Throwable $e)
    {
        \Log::error("SendBookingCancellationSmsJob failed for booking ID {$this->booking->id}: {$e->getMessage()}");
    }
}
