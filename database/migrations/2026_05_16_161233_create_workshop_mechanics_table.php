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
        Schema::create('workshop_mechanics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->references('id')->on('provider_profiles')->onDelete('cascade');
            $table->string('name');
            $table->string('user_name');
            $table->string('password');
            $table->string('phone_number');
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['offline','in_job','available'])->default('available'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_mechanics');
    }
};
