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
        Schema::create('pricing_models', function (Blueprint $table) {
            $table->id('model_id');
            $table->string('service_type');
            $table->decimal('base_fee', 10, 2);
            $table->decimal('fee_per_km', 10, 2);
            $table->jsonb('parameters')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_models');
    }
};
