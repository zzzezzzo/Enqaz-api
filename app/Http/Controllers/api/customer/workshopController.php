<?php

namespace App\Http\Controllers\api\customer;
use App\Models\ProviderProfile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class workshopController extends Controller
{
    public function show($providerId)
    {
        // Assuming you have a Workshop model that has a relationship with the Provider model
        $workshop = ProviderProfile::where('id', $providerId)->first();
        if (!$workshop) {
            return response()->json(['message' => 'Workshop not found'], 404);
        }

        return response()->json([
            'opening_time' => $workshop->opening_time,
            'closing_time' => $workshop->closing_time,
        ]);
    }
}
