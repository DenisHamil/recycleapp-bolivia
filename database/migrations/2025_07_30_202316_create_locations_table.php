<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('department');
            $table->string('province');
            $table->string('municipality');
            $table->timestamps();

            $table->unique(['department', 'province', 'municipality'], 'location_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
