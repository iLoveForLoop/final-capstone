<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    //  public function bookings()
    // {
    //     return $this->hasMany(Booking::class);
    // }

    //NEW ADDED NOTIFICATIONS
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function getUnreadNotificationCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }

    protected $guarded = [];
}
