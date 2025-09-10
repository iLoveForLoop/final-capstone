<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;


    protected $fillable = [
        'vendor_id',
        'booking_id',
        'user_id',
        'rating',
        'comment',
        'response',
        'responded_at'
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'responded_at' => 'datetime',
    ];

}
