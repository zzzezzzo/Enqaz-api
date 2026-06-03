<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'customer_id',
        'provider_id',
        'vehicle_id',
        'service_id',
        'latitude',
        'longitude',
        'description',
        'status_id',
        'assigned_mechanic_id',
        'dispatch_status',
        'mechanic_latitude',
        'mechanic_longitude',
        'scheduled_date',
        'scheduled_starts_at',
        'scheduled_ends_at',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function status()
    {
        return $this->belongsTo(RequestStatus::class, 'status_id');
    }
    public function ratings()
    {
        return $this->hasOne(Rating::class, 'service_request_id');
    }
    public function assignedMechanic()
    {
        return $this->belongsTo(WorkshopMechanic::class, 'assigned_mechanic_id');
    }
    
}
