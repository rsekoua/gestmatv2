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
        Schema::create('accessoire_attribution', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attribution_id')->constrained('attributions')->onDelete('cascade');
            $table->foreignUuid('accessory_id')->constrained('accessories')->onDelete('restrict');
            $table->enum('statut_att', ['fourni'])->default('fourni');
            $table->enum('statut_res', ['restitué', 'manquant'])->nullable();
            $table->timestamps();
            
            $table->index('attribution_id');
            $table->index('accessory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessoire_attribution');
    }
};
