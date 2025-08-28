<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    /** @use HasFactory<\Database\Factories\VendorFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // public function category()
    // {
    //     return $this->belongsTo(ServiceCategory::class);
    // }

    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category_vendor');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class); // vendor_id on reviews
    }

    public function averageRating()
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? number_format($avg, 1) : 0;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class); // vendor_id on bookings
    }

    public function dishes(){
        return $this->hasMany(Dish::class);
    }


    //NOTIFICATIONS
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
