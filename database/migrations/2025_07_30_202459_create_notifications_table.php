<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('title');
            $table->text('message');
            $table->string('type');
            $table->uuid('related_id')->nullable();

            // Polimorfismo liviano y UX extra
            $table->string('related_type', 191)->nullable();
            $table->json('payload')->nullable();
            $table->string('status', 32)->default('new'); // new|processing|done
            $table->unsignedTinyInteger('priority')->default(3); // 1=alta..5=baja
            $table->string('action_url', 255)->nullable();

            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'is_read', 'created_at'], 'notif_user_read_created_idx');
            $table->index(['related_type', 'related_id'], 'notif_related_idx');
            $table->index(['type'], 'notif_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
