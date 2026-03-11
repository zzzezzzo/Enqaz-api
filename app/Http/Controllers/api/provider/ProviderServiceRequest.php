<?php

namespace App\Http\Controllers\api\provider;

use App\Http\Controllers\Controller;

use App\Models\ServiceRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProviderServiceRequest extends Controller
{
    public function index(){
        $requests = ServiceRequest::where('provider_id' , Auth::user()->id )->get();
        $requests->load([
            'customer:id,name,email,phone',
            'vehicle:id,plate_number,model,brand',
            'service:id,name,description',
            'status:id,name'
        ]);
        return response()->json([
                'message' => 'Provider profile updated successfully',
                'data' => $requests
            ], 200);
    }
}
