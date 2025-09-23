<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recycler_proposals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('donation_id');
            $table->uuid('collector_id');
            $table->date('proposed_date')->nullable();
            $table->time('proposed_time')->nullable();
            $table->enum('status', ['waiting', 'accepted', 'rejected', 'in_progress'])->default('waiting');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
            $table->foreign('collector_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recycler_proposals');
    }
};
