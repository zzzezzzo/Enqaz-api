<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public function providers()
    {
        return $this->belongsToMany(ProviderProfile::class, 'provider_services', 'service_id', 'provider_id');
    }
}
