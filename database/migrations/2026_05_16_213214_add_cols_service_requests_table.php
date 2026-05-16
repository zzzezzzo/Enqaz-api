<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::table('service_requests', function (Blueprint $table) {

            // الميكانيكي المسؤول عن الطلب
            $table->foreignId('assigned_mechanic_id')
                ->nullable()
                ->after('provider_id')
                ->constrained('workshop_mechanics')
                ->nullOnDelete();

            // حالة تحرك/تنفيذ الطلب
            $table->enum('dispatch_status', [
                'unassigned',
                'assigned',
                'en_route',
                'arrived',
                'in_service',
                'completed',
            ])->default('unassigned')
                ->after('assigned_mechanic_id');

            // آخر موقع للميكانيكي
            $table->decimal('mechanic_latitude', 10, 7)
                ->nullable()
                ->after('dispatch_status');

            $table->decimal('mechanic_longitude', 10, 7)
                ->nullable()
                ->after('mechanic_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_mechanic_id']);
            $table->dropColumn(['assigned_mechanic_id', 'dispatch_status', 'mechanic_latitude', 'mechanic_longitude']);
        });
    }
};
