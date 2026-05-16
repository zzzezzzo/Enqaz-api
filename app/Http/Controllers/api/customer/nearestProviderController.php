<?php

namespace App\Http\Controllers\api\customer;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\Service;
use Illuminate\Http\Request;

class nearestProviderController extends Controller
{
    public function index(Request $request)
    {
        $latitude = $request->query('Latitude', $request->input('latitude'));
        $longitude = $request->query('longitude', $request->input('longitude'));

        if (!$latitude || !$longitude) {
            return response()->json([
                'message' => 'Latitude and longitude are required'
            ], 400);
        }

        // 1. الحصول على الوقت الحالي بالساعات والدقائق والثواني (صيغة 24 ساعة)
        // تأكد أن الـ timezone في ملف config/app.php مضبوط على دولتك
        $now = now()->format('H:i:s'); 

        // 2. حساب المسافة وفلترة البيانات
        $providers = \DB::table('provider_profiles')
            ->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', 10)
            ->where(function ($query) use ($now) {
                $query->whereRaw("
                    CASE 
                        -- الحالة الأولى: المواعيد الطبيعية (مثلاً من 9 صباحاً إلى 10 مساءً)
                        WHEN opening_time < closing_time THEN ? BETWEEN opening_time AND closing_time
                        -- الحالة الثانية: المواعيد الممتدة بعد منتصف الليل (مثلاً من 6 مساءً إلى 2 صباحاً)
                        ELSE ? >= opening_time OR ? <= closing_time
                    END
                ", [$now, $now, $now]);
            })
            ->orderBy('distance')
            ->get();

        // 3. التحقق من النتائج
        if ($providers->isEmpty()) {
            return response()->json([
                'message' => 'No open providers found within 10 kilometers'
            ], 404);
        }

        return response()->json([
            'message' => 'Nearest open providers retrieved successfully',
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
