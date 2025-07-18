<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Dish extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];
    public function cateringPackages() {
        return $this->belongsToMany(CateringPackage::class, 'catering_package_dish')
                    ->withPivot('quantity', 'optional')
                    ->withTimestamps();
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }
}