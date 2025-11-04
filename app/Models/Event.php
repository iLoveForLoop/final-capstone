<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getStatusAttribute()
    {
        $statuses = $this->bookings->pluck('status');

        if ($statuses->every(fn($s) => $s === 'confirmed')) {
            return 'confirmed';
        }

        if ($statuses->every(fn($s) => $s === 'completed')) {
            return 'completed';
        }

        if ($statuses->contains('pending')) {
            return 'pending';
        }

        if ($statuses->contains('cancelled')) {
            return 'cancelled';
        }

        return 'unknown'; // fallback
    }

        public function getStartAttribute()
        {
            if ($this->event_time) {
                return $this->event_date->format('Y-m-d') . 'T' . date('H:i:s', strtotime($this->event_time));
            }
            return $this->event_date->format('Y-m-d');
        }


        protected $casts = [
        'event_date' => 'date',
        'event_time' => 'string'
    ];

    protected $guarded = [];
}