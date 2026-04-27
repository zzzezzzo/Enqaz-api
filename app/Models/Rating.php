<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'service_request_id',
        'customer_id',
        'provider_id',
        'rating',
        'comment',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
