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
        Schema::create('discharge_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attribution_id')->constrained('attributions')->onDelete('cascade');
            $table->enum('type', ['attribution', 'restitution']);
            $table->string('numero_decharge', 50);
            $table->string('file_path', 500);
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index('attribution_id');
            $table->index('numero_decharge');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discharge_documents');
    }
};
