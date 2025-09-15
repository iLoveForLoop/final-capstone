<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type',
        'attachments',
        'read_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }

    public function markAsReadBy($userId)
    {
        return $this->reads()->updateOrCreate(
            ['user_id' => $userId],
            ['read_at' => now()]
        );
    }

    public function isReadBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}