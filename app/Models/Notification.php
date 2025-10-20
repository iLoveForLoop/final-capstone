<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'type', 'title', 'message', 'data', 'read_at',
        'priority', 'action_url', 'client_id', 'recipient_type'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    protected $appends = ['is_read', 'time_ago'];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Accessors
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId)->where('recipient_type', 'vendor');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('client_id', $userId)->where('recipient_type', 'client');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    // Static methods - Keep backward compatibility
    public static function createForVendor($vendorId, $type, $title, $message, $data = null, $priority = 'normal', $actionUrl = null)
    {
        return static::create([
            'vendor_id' => $vendorId,
            'recipient_type' => 'vendor',
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'action_url' => $actionUrl
        ]);
    }

    public static function createForUser($userId, $type, $title, $message, $data = null, $priority = 'normal', $actionUrl = null)
    {
        return static::create([
            'client_id' => $userId,
            'recipient_type' => 'client',
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'action_url' => $actionUrl
        ]);
    }

    // Constants - Add client notification types
    const TYPE_BOOKING_RECEIVED = 'booking_received';
    const TYPE_BOOKING_COMPLETED = 'booking_completed';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_DECLINED = 'booking_declined';
    const TYPE_BOOKING_CONFIRMED = 'booking_confirmed';
    const TYPE_SERVICE_UPDATED = 'service_updated';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_REVIEW_RECEIVED = 'review_received';
    const TYPE_SYSTEM_ALERT = 'system_alert';

    // Client-specific types
    const TYPE_BOOKING_CONFIRMED_CLIENT = 'booking_confirmed_client';
    const TYPE_BOOKING_UPDATED = 'booking_updated';
    const TYPE_BOOKING_REMINDER = 'booking_reminder';
    const TYPE_PAYMENT_CONFIRMED = 'payment_confirmed';
    const TYPE_VENDOR_MESSAGE = 'vendor_message';
    const TYPE_BOOKING_COMPLETED_CLIENT = 'booking_completed_client';
    const TYPE_BOOKING_SUBMITTED = 'booking_submitted';
    const TYPE_BOOKING_IN_PROGRESS = 'booking_in_progress';
    const TYPE_BOOKING_CANCELLED_CLIENT = 'booking_cancelled_client';
    const TYPE_BOOKING_DECLINED_CLIENT = 'booking_declined_client';

    public static function getTypes()
    {
        return [
            // Vendor types
            self::TYPE_BOOKING_RECEIVED,
            self::TYPE_BOOKING_COMPLETED,
            self::TYPE_BOOKING_CANCELLED,
            self::TYPE_BOOKING_DECLINED,
            self::TYPE_BOOKING_CONFIRMED,
            self::TYPE_SERVICE_UPDATED,
            self::TYPE_PAYMENT_RECEIVED,
            self::TYPE_REVIEW_RECEIVED,
            self::TYPE_SYSTEM_ALERT,

            // Client types
            self::TYPE_BOOKING_CONFIRMED_CLIENT,
            self::TYPE_BOOKING_UPDATED,
            self::TYPE_BOOKING_REMINDER,
            self::TYPE_PAYMENT_CONFIRMED,
            self::TYPE_VENDOR_MESSAGE,
            self::TYPE_BOOKING_COMPLETED_CLIENT,
            self::TYPE_BOOKING_SUBMITTED,
            self::TYPE_BOOKING_IN_PROGRESS,
            self::TYPE_BOOKING_CANCELLED_CLIENT,
            self::TYPE_BOOKING_DECLINED_CLIENT,
        ];
    }
}