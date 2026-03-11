<?php

namespace App\Http\Controllers\api\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class nearestProviderController extends Controller
{
    public function index(Request $request){
        // dd($request->all());    
        // $latitude =$request->latitude;
        $latitude = $request->query('Latitude', $request->input('Latitude'));
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
}
