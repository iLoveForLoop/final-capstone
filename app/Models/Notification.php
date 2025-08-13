<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'type', 'title', 'message', 'data', 'read_at', 'priority', 'action_url'
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
        return $query->where('vendor_id', $vendorId);
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

    // Methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    // Static methods
    public static function createForVendor($vendorId, $type, $title, $message, $data = null, $priority = 'normal', $actionUrl = null)
    {
        return static::create([
            'vendor_id' => $vendorId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'action_url' => $actionUrl
        ]);
    }

    // Constants
    const TYPE_BOOKING_RECEIVED = 'booking_received';
    const TYPE_BOOKING_COMPLETED = 'booking_completed';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_CONFIRMED = 'booking_confirmed';
    const TYPE_SERVICE_UPDATED = 'service_updated';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_REVIEW_RECEIVED = 'review_received';
    const TYPE_SYSTEM_ALERT = 'system_alert';

    public static function getTypes()
    {
        return [
            self::TYPE_BOOKING_RECEIVED,
            self::TYPE_BOOKING_COMPLETED,
            self::TYPE_BOOKING_CANCELLED,
            self::TYPE_BOOKING_CONFIRMED,
            self::TYPE_SERVICE_UPDATED,
            self::TYPE_PAYMENT_RECEIVED,
            self::TYPE_REVIEW_RECEIVED,
            self::TYPE_SYSTEM_ALERT,
        ];
    }
}
