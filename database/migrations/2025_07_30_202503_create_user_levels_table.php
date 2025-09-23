<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_levels', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['bronze', 'silver', 'gold'])->unique();
            $table->integer('min_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_levels');
    }
};
