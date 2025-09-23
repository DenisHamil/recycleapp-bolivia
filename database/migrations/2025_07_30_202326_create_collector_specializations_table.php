<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collector_specializations', function (Blueprint $table) {
            $table->uuid('id')->primary();  // Clave primaria de tipo UUID
            $table->uuid('collector_id');   // 'collector_id' como UUID
            $table->uuid('category_id');    // 'category_id' también como UUID

            // Relación con la tabla 'users' (recolector) y 'categories' (categorías)
            $table->foreign('collector_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collector_specializations');
    }
};
