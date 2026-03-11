<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function providers()
    {
        return $this->belongsToMany(ProviderProfile::class, 'provider_services', 'service_id', 'provider_id');
    }
}
