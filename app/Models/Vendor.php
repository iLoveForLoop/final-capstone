<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vendor extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\VendorFactory> */
    use HasFactory, InteractsWithMedia;

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

    public function ratingBreakdown()
    {
        // group reviews by rating and count them
        $counts = $this->reviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // make sure all 1–5 ratings exist, even if zero
        $breakdown = [];
        foreach (range(1, 5) as $star) {
            $breakdown[$star] = $counts[$star] ?? 0;
        }

        return $breakdown;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class); // vendor_id on bookings
    }

    public function getCompletedBookingsCount(){
        return $this->bookings()->where('status', 'completed')->count();
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

    protected $casts = [
        'specialties' => 'array'
    ];

    protected $guarded = [];
}