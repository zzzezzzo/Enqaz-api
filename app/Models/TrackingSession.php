<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingSession extends Model
{
    protected $fillable = [
        'service_request_id',
        'provider_id',
        'started_at',
        'ended_at',
        
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
