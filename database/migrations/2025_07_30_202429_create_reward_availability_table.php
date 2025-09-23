<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reward_availability', function (Blueprint $table) {
            $table->id();
            $table->uuid('reward_id');
            $table->string('department', 100);

            $table->foreign('reward_id')->references('id')->on('rewards_store')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_availability');
    }
};
