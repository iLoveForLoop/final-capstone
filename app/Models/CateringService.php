<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringService extends Model
{

    protected $guarded = [];

    public function service() {
        return $this->belongsTo(Service::class);
    }

public function packages() {
        return $this->hasMany(CateringPackage::class);
    }
}