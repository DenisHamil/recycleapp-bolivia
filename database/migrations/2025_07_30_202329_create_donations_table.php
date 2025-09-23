<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('donor_id');
            $table->uuid('collector_id')->nullable();
            $table->uuid('category_id');
            $table->uuid('subcategory_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('estimated_weight', 6, 2)->nullable();
            $table->decimal('confirmed_weight', 6, 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address_description')->nullable();
            // NUEVO: rango de fecha y hora
            $table->date('available_from_date')->nullable();
            $table->date('available_until_date')->nullable();
            $table->time('available_from_time')->nullable();
            $table->time('available_until_time')->nullable();

            $table->enum('state', ['pending', 'accepted', 'cancelled', 'completed'])->default('pending');
            $table->text('cancel_reason')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->boolean('confirmed_by_collector')->default(false);
            $table->timestamps();

            $table->foreign('donor_id')->references('id')->on('users');
            $table->foreign('collector_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
