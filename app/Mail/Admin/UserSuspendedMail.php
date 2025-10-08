<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $user;
    public $suspensionType;
    public $suspendedAt;
    public $suspensionDuration;
    public $suspensionReason;
    public $role;
    public function __construct(User $user, $suspensionType, $role)
    {
        $this->user = $user;
        $this->suspensionType = $suspensionType;
        $this->suspendedAt = now();
        $this->suspensionReason = $user->suspension_reason ?? 'No reason provided';
        $this->role = $role;

        if ($suspensionType === 'temporary' && $user->suspended_until) {
            $this->suspensionDuration = $this->suspendedAt->diffInDays($user->suspended_until) . ' days';
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Suspended Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.user_suspended',
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