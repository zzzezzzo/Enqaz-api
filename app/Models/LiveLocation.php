<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveLocation extends Model
{
    protected $fillable = [
        'provider_id',
        'latitude',
        'longitude',
        'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
