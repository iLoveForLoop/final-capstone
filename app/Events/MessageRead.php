<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $message;
    public $readerId;

    public function __construct(Message $message, $readerId)
    {
        $this->message = $message;
        $this->readerId = $readerId;
    }

    public function broadcastOn()
    {
        // Notify sender that their message was read
        return new PrivateChannel('user.' . $this->message->user_id);
    }

    public function broadcastAs()
    {
        return 'MessageRead';
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'reader_id' => $this->readerId,
        ];
    }
}
