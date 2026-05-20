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
            $table->enum('request_type', ['immediate', 'scheduled'])
                ->default('immediate')
                ->after('status_id');
            // تاريخ المعاد
            $table->date('scheduled_date')->nullable()->after('request_type');
            // من – إلى (24 ساعة في DB)
            $table->time('scheduled_starts_at')->nullable()->after('scheduled_date');
            $table->time('scheduled_ends_at')->nullable()->after('scheduled_starts_at');
            $table->index(['provider_id', 'scheduled_date', 'scheduled_starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'scheduled_date', 'scheduled_starts_at', 'scheduled_ends_at']);
        });
    }
};
