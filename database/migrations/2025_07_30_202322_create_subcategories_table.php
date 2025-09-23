<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subcategories', function (Blueprint $table) {
            $table->uuid('id')->primary();  // Usamos UUID para la clave primaria
            $table->uuid('category_id');    // Usamos UUID para 'category_id'
            $table->string('name', 100);
            $table->timestamps();

            // Relación con la tabla 'categories'
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
