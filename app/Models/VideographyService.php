<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideographyService extends Model
{

    protected $guarded = [];
    protected $casts = [
        'specifications' => 'array',
        'studio_shoot_available' => 'boolean'
    ];

    public function service(){
        return $this->belongsTo(Service::class);
    }
}