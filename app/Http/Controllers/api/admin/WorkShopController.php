<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;

class WorkShopController extends Controller
{
    public function pendingWorkshops(){
        $pendingWorkshops = ProviderProfile::with('services')->paginate(10);
        return response()->json($pendingWorkshops);
    }

    public function activateWorkShop( $id){
        $workshop = ProviderProfile::find($id);
        if(!$workshop){
            return response()->json(['message' => 'Workshop not found'], 404);
        }
        if($workshop->is_available){
            $workshop->is_available = false;
            $workshop->save();
            return response()->json(['message' => 'Workshop is not activated']);
        }
        $workshop->is_available = true;
        $workshop->save();
        return response()->json(['message' => 'Workshop activated successfully']);
    }
}
