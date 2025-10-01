<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity, HasRoles,InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'suspended_until',
        'suspension_reason',
        'ban_reason'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_until' => 'datetime',
        ];
    }

    // public function vendors()
    // {
    //     return $this->hasMany(Vendor::class);
    // }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function client(){
        return $this->hasOne(Client::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Service::class, 'favorites')->withTimestamps();
    }

    public function events(){
        return $this->hasMany(Event::class);
    }

    public function getIsVendorAttribute()
    {
        return $this->vendor()->exists();
    }

    public function getIsClientAttribute()
    {
        return $this->client()->exists();
    }

    //for suspensions and bans
    public function isBanned(): bool
    {
        // Permanent ban
        if ($this->status === 'banned') {
            return true;
        }

        return false;
    }

    public function isSuspended(): bool
    {
        // Active suspension
        if ($this->status === 'suspended' && $this->suspended_until && $this->suspended_until->isFuture()) {
            return true;
        }

        // If suspension expired → reset to active
        if ($this->status === 'suspended' && $this->suspended_until && $this->suspended_until->isPast()) {
            $this->update(['status' => 'active', 'suspended_until' => null, 'ban_reason' => null]);
            return false;
        }

        return false;
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'status', 'suspended_until'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User {$eventName}");
    }

}