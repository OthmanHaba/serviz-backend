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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id('provider_id');
            $table->string('name');
            $table->enum('provider_type', ['tow_truck', 'mechanic', 'gas_delivery']);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->decimal('service_radius_km', 8, 2);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
