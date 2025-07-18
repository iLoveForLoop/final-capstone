<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory, InteractsWithMedia;

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // public function availabilitySlots()
    // {
    //     return $this->hasMany(AvailabilitySlot::class);
    // }

    public function cateringService() {
        return $this->hasOne(CateringService::class);
    }

    protected $guarded = [];
}