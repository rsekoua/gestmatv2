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
        Schema::create('maintenance_definitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('materiel_type_id')->constrained('materiel_types')->onDelete('cascade');
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('frequency_days');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('maintenance_operations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('materiel_id')->constrained('materiels')->onDelete('cascade');
            $table->foreignUuid('maintenance_definition_id')->constrained('maintenance_definitions')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->date('scheduled_at');
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('performed_by')->nullable()->constrained('users')->onDelete('set null'); // Assuming users table exists for auth
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_operations');
        Schema::dropIfExists('maintenance_definitions');
    }
};
