<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(User::class, 'admin_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_sessions');
    }
};
