<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

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

    protected $guarded = [];
}
