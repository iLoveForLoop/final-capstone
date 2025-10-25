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

    protected $casts = [
        'specifications' => 'array',
    ];

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

    public function photographyService() {
        return $this->hasOne(PhotographyService::class);
    }

    public function videographyService() {
        return $this->hasOne(VideographyService::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function isFavoritedBy(?User $user = null): bool
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return false;
        }

        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }


    protected $guarded = [];


}
