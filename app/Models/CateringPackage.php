<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringPackage extends Model
{

    protected $guarded = [];
    public function cateringService() {
        return $this->belongsTo(CateringService::class);
    }

public function dishes() {
        return $this->belongsToMany(Dish::class, 'catering_package_dish')
                    ->withPivot('quantity', 'optional')
                    ->withTimestamps();
    }
}