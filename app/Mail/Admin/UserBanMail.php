<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserBanMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $user;
    public $banDate;
    public $banReason;
    public $role;
    public function __construct(User $user, $role)
    {
        $this->user = $user;
        $this->banDate = now();
        $this->banReason = $user->ban_reason ?? 'No reason provided';
        $this->role = $role;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Permanently Banned - Eventory',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // dd('here');
        return new Content(
            markdown: 'emails.admin.user_ban',
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
