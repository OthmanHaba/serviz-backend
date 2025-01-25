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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('provider_id')->nullable()->constrained('service_providers', 'provider_id');
            $table->string('service_type');
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled']);
            $table->json('pickup_location')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->timestamp('requested_at')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
