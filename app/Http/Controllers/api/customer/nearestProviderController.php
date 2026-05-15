<?php

namespace App\Http\Controllers\api\customer;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\Service;
use Illuminate\Http\Request;

class nearestProviderController extends Controller
{
    public function index(Request $request){
  
        // $latitude =$request->latitude;
        $latitude = $request->query('Latitude', $request->input('latitude'));
        $longitude = $request->query('longitude', $request->input('longitude'));
        // $longitude = $request->query('longitude');
        if (!$latitude || !$longitude) {
            return response()->json([
                'message' => 'Latitude and longitude are required'
            ], 400);
        }
        // Calculate the distance using the Haversine formula
        $providers = \DB::table('provider_profiles')
            ->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', 10)
            ->orderBy('distance')
            ->get();
        if ($providers->isEmpty()) {
            return response()->json([
                'message' => 'No providers found within 10 kilometers'
            ], 404);
        }
        return response()->json([
            'message' => 'Nearest providers retrieved successfully',
            'data' => $providers
        ], 200);
    }
    public function services($id){
        // get the service provider 
        $services = ProviderProfile::findOrFail($id)->services;
        return response()->json([
            'message' => 'the service to related provider',
            'data' => $services
        ]);
    }
}
