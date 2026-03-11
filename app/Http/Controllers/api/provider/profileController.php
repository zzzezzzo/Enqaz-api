<?php

namespace App\Http\Controllers\api\provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderProfileRequest;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NunoMaduro\Collision\Provider;

class profileController extends Controller
{
    public function index(){
        $profile = ProviderProfile::with('services')->where('user_id', auth()->id())->first();
        if (!$profile) {
            return response()->json([
                'message' => 'Provider profile not found for this user'
            ], 404);
        }
        return response()->json([
            'message' => 'Provider profile retrieved successfully',
            'data' => $profile
        ], 200);   
    }
    public function createProfile(StoreProviderProfileRequest $request){ 
        // Validate the incoming request data
        DB::beginTransaction();
        try {
            if (ProviderProfile::where('user_id', auth()->id())->exists()) {
                return response()->json([
                    'message' => 'Provider profile already exists for this user'
                ], 400);
            }
            $profile = ProviderProfile::create([
                'user_id' => auth()->id(),
                'workShopName' => $request->name,
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_available' => true, // Default to available when creating a profile
            ]);
            // record the services provided by the provider 
            foreach($request->services as $service){
                ProviderService::create([
                    'provider_id' => $profile->id,
                    'service_id' => $service,
                ]);
            }
            DB::commit();
            $data = ProviderProfile::with('services')->where('user_id', auth()->id())->first();
            return response()->json([
                'message' => 'Provider profile created successfully',
                'data' => $data
            ], 201);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create provider profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function editProfile(){
        $profile = ProviderProfile::with('services')->where('user_id', auth()->id())->first();
        if (!$profile) {
            return response()->json([
                'message' => 'Provider profile not found for this user'
            ], 404);
        }
        return response()->json([
            'message' => 'Provider profile retrieved successfully',
            'data' => $profile
        ], 200);
    }
    public function updateProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $profile = ProviderProfile::where('user_id', auth()->id())->first();
            if (!$profile) {
                return response()->json([
                    'message' => 'Provider profile not found for this user'
                ], 404);
            }
            $profile->update([
                'workShopName' => $request->name,
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
            $profile->services()->sync($request->services);
            DB::commit();
            $data = ProviderProfile::with('services')
                ->where('user_id', auth()->id())
                ->first();
            return response()->json([
                'message' => 'Provider profile updated successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update provider profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
