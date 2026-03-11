<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestStatus;

class RequestStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'pending'],
            ['name' => 'accepted'],
            ['name' => 'in_progress'],
            ['name' => 'completed'],
            ['name' => 'cancelled'],
            ['name' => 'rejected']
        ];

        foreach ($statuses as $status) {
            RequestStatus::create($status);
        }
    }
}
