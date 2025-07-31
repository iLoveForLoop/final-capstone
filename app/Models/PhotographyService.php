<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotographyService extends Model
{
    protected $guarded = [];

    protected $casts = [
        'deliverables' => 'array',
        'specifications' => 'array'
    ];

    public function service(){
        return $this->belongsTo(Service::class);
    }
}
