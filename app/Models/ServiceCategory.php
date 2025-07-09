<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceCategoryFactory> */
    use HasFactory;

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'service_category_vendor');
    }


    protected $guarded = [];

}
