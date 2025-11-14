<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('emploi', 100);
            $table->string('email', 255)->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('fonction', 100)->nullable();
            $table->timestamps();

            $table->index('service_id');
            $table->index('email');
            $table->index(['service_id', 'email'], 'employees_service_email_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
