<?php

namespace App\Jobs;

use App\Mail\VendorApplicationAcceptedMail;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendVendorApplicationAcceptedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $vendor;

    public $tries = 3;
    public $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

         \Log::warning("Vendor Accepted Running");

        $vendor = $this->vendor->load(['user']);
        $user = $vendor->user;

        if (!$user || !$user->email) {
            \Log::warning("User email missing for vendor application accepted - Vendor ID: {$vendor->id}");
            return;
        }

        try {
            Mail::to($user->email)->send(new VendorApplicationAcceptedMail($vendor));
            \Log::info("Vendor application accepted email sent successfully to vendor ID {$vendor->id}");
        } catch (Throwable $e) {
            \Log::error("Failed to send vendor application accepted email for vendor ID {$vendor->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $e)
    {
        \Log::error("SendVendorApplicationAcceptedEmailJob failed for vendor ID {$this->vendor->id}: {$e->getMessage()}");
    }
}