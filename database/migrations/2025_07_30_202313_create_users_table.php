<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Ahora estamos usando una columna autoincremental
            $table->uuid('id')->primary();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'donor', 'collector']);
            $table->enum('donor_type', ['family', 'organization'])->nullable();
            $table->string('organization_name', 100)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('representative_name', 100)->nullable();
            $table->text('profile_image_path')->nullable();
            $table->string('department', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address')->nullable();
            $table->enum('level', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
