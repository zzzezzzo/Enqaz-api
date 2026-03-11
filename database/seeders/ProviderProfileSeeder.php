<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProviderProfile;
use App\Models\User;
use App\Models\Service;

class ProviderProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create provider role
        $providerRole = \App\Models\Role::firstOrCreate(['name' => 'provider']);
        
        // Create provider users first
        $providers = [
            [
                'name' => 'محمد أحمد',
                'email' => 'provider1@example.com',
                'phone' => '01234567890',
                'role_id' => $providerRole->id, // Use actual role ID
                'is_active' => true,
                'password' => bcrypt('password123')
            ],
            [
                'name' => 'علي محمود',
                'email' => 'provider2@example.com', 
                'phone' => '01234567891',
                'role_id' => $providerRole->id, // Use actual role ID
                'is_active' => true,
                'password' => bcrypt('password123')
            ],
            [
                'name' => 'أحمد خالد',
                'email' => 'provider3@example.com',
                'phone' => '01234567892', 
                'role_id' => $providerRole->id, // Use actual role ID
                'is_active' => true,
                'password' => bcrypt('password123')
            ]
        ];

        $createdProviders = [];
        foreach ($providers as $providerData) {
            $provider = User::firstOrCreate(
                ['email' => $providerData['email']],
                $providerData
            );
            $createdProviders[] = $provider;
        }

        // Create provider profiles
        $profiles = [
            [
                'user_id' => $createdProviders[0]->id,
                'workShopName' => 'ورشة محمد للصيانة الشاملة',
                'description' => 'متخصصون في صيانة السيارات اليابانية والألمانية، خبرة أكثر من 10 سنوات',
                'latitude' => '30.044420',
                'longitude' => '31.235712',
                'is_available' => true,
                'average_rating' => '4.5'
            ],
            [
                'user_id' => $createdProviders[1]->id,
                'workShopName' => 'مركز علي لتغيير الزيوت',
                'description' => 'أفضل مركز لتغيير الزيوت والفلاتر، استخدام زيوت أصلية فقط',
                'latitude' => '30.050000',
                'longitude' => '31.240000',
                'is_available' => true,
                'average_rating' => '4.2'
            ],
            [
                'user_id' => $createdProviders[2]->id,
                'workShopName' => 'ورشة أحمد للكهرباء والبطاريات',
                'description' => 'متخصصون في مشاكل الكهرباء وتغيير البطاريات، خدمة منزلية متاحة',
                'latitude' => '30.060000',
                'longitude' => '31.250000',
                'is_available' => true,
                'average_rating' => '4.8'
            ]
        ];

        foreach ($profiles as $profileData) {
            ProviderProfile::firstOrCreate(
                ['user_id' => $profileData['user_id']],
                $profileData
            );
        }

        // Assign services to providers
        $services = Service::all();
        $providerProfiles = ProviderProfile::all();

        foreach ($providerProfiles as $index => $profile) {
            // Assign different services to each provider
            $serviceIds = $services->slice($index * 3, 3)->pluck('id');
            if ($serviceIds->count() > 0) {
                $profile->services()->attach($serviceIds);
            }
        }
    }
}
