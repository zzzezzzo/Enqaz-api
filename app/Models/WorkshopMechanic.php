<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Model;

class WorkshopMechanic extends Model
{
    use HasApiTokens;
    protected $fillable = [
        'workshop_id',
        'name',
        'user_name',
        'password',
        'phone_number',
        'is_active',
        'status',
    ];
    protected $hidden = [
        'password',
    ];

    public function workshop()
    {
        return $this->belongsTo(ProviderProfile::class, 'workshop_id');
    }
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_mechanic_id');
    }
}
