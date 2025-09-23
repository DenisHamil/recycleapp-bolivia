<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rewards_store', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->text('image_path')->nullable();
            $table->unsignedBigInteger('reward_category_id')->nullable();
            $table->uuid('commerce_id')->nullable();
            $table->integer('points_required');
            $table->integer('stock')->default(0);
            $table->boolean('is_monthly_promo')->default(false);
            $table->timestamps();

            $table->foreign('reward_category_id')->references('id')->on('reward_categories');
            $table->foreign('commerce_id')->references('id')->on('commerce');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards_store');
    }
};
