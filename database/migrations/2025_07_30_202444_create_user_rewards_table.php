<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('reward_id');
            $table->enum('status', ['pending', 'delivered', 'cancelled'])->default('pending');
            $table->timestamp('redeemed_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reward_id')->references('id')->on('rewards_store');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_rewards');
    }
};
