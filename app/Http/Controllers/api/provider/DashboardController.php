<?php

namespace App\Http\Controllers\api\provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\ServiceRequest;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $serviceStatus = $this->getServiceStatus();
        $incomeRequest = $this->incomeRequest();
        return response()->json([
            'service_status' => $serviceStatus,
            'income_request' => $incomeRequest
        ]);
    }
    private function getServiceStatus(){
        return[
            'total_requests_today' => ServiceRequest::whereDate('created_at', today())->count(),
            'active_jobs' => ServiceRequest::whereHas('status',function($q){
                $q->where('name','accepted');
            })->count()
        ];
    }
    private function incomeRequest()
    {
        $provider = ProviderProfile::where('user_id', auth()->id())->firstOrFail();

        $lat = $provider->latitude;
        $lng = $provider->longitude;

        $requests = ServiceRequest::selectRaw("
                service_requests.*,
                (6371 * acos(
                    cos(radians(?)) 
                    * cos(radians(service_requests.latitude)) 
                    * cos(radians(service_requests.longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(service_requests.latitude))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->whereDate('created_at', today())
            ->where('provider_id' , $provider->id)
            ->whereHas('status', function ($q) {
                $q->where('name', 'pending');
            })
            ->with(['customer', 'service'])
            ->orderBy('distance')
            ->get()
            ->map(function ($req) {
            return [
                'id' => $req->id,
                'latitude' => $req->latitude,
                'longitude' => $req->longitude,
                'customer_name' => $req->customer?->name,
                'description' => $req->description,
                'service_name' => $req->service?->name,
                'distance' => round($req->distance, 2) . ' km',
                'minutes_ago' => $req->created_at
                    ? now()->diffInMinutes($req->created_at) . ' min'
                    : null,
            ];
        });
        return [
            'provider_id' => Auth::id(),
            'workShop_location'=>[
                'latitude' => $lat,
                'longitude' => $lng
            ],
            'requests' => $requests
        ];
    }
}
