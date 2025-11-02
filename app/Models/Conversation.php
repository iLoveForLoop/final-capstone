<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'event_id',
        'participants',
        'last_message_at'
    ];

    protected $casts = [
        'participants' => 'array',
        'last_message_at' => 'datetime'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function users()
    {
        return User::whereIn('id', $this->participants);
    }

    public function hasParticipant($userId)
    {
        return in_array($userId, $this->participants);
    }

    public function addParticipant($userId)
    {
        if (!$this->hasParticipant($userId)) {
            $participants = $this->participants;
            $participants[] = $userId;
            $this->update(['participants' => $participants]);
        }
    }

    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

    public function getDisplayName($currentUserId)
    {
        // For direct conversations, show the other participant's name
        if ($this->type === 'direct') {
            $otherParticipant = $this->users()
                ->where('users.id', '!=', $currentUserId)
                ->first();

            return $otherParticipant ? $otherParticipant->name : 'Unknown User';
        }

        // For group conversations or support, use title if available
        if ($this->title) {
            return $this->title;
        }

        // For support conversations without title
        if ($this->type === 'support') {
            return 'Support Chat';
        }

        // For group conversations, show participant count
        $participantCount = count($this->participants ?? []);
        return "Group Chat ({$participantCount} members)";
    }
}