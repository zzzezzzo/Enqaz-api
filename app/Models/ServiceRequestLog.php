<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestLog extends Model
{
    protected $fillable = [
        'service_request_id',
        'old_status_id',
        'new_status_id',
        'changed_by',
        'comment',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function oldStatus()
    {
        return $this->belongsTo(RequestStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(RequestStatus::class, 'new_status_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
