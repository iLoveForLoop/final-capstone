<?php

namespace App\Mail\Vendor;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorBookingCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $bookingId;
    public $booking;
    public $additionalComment;
    public $cancellationReason;

    public function __construct(int $bookingId, $cancellationReason, $additionalComment)
    {
        $this->bookingId = $bookingId;
        $this->cancellationReason = $cancellationReason;
        $this->additionalComment = $additionalComment;

        $this->booking = Booking::with(['service.vendor.user', 'user'])
        ->findOrFail($this->bookingId);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Cancelled by Customer - ' . $this->booking->service->name . ' - Eventory',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        return new Content(
            markdown: 'emails.vendor.booking_cancelled',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}