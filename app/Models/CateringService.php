<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringService extends Model
{

    protected $guarded = [];

    protected $casts = [
        'service_area' => 'array',
        'specifications' => 'array',
        'dishes' => 'array',
        'dish_selection_limits' => 'array'
    ];



    public function service() {
        return $this->belongsTo(Service::class);
    }

public function packages() {
        return $this->hasMany(CateringPackage::class);
    }


}