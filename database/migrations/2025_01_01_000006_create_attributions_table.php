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
        Schema::create('attributions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('materiel_id')->constrained('materiels')->onDelete('restrict');
            $table->foreignUuid('employee_id')->constrained('employees')->onDelete('restrict');
            $table->date('date_attribution');
            $table->date('date_restitution')->nullable();
            $table->string('numero_decharge_att', 50)->unique()->nullable();
            $table->string('numero_decharge_res', 50)->unique()->nullable();
            
            // Champs pour l'attribution
            $table->text('observations_att')->nullable();
            
            // Champs pour la restitution
            $table->text('observations_res')->nullable();
            $table->enum('etat_general_res', ['excellent', 'bon', 'moyen', 'mauvais'])->nullable();
            $table->enum('etat_fonctionnel_res', ['parfait', 'defauts_mineurs', 'dysfonctionnements', 'hors_service'])->nullable();
            $table->json('dommages_res')->nullable();
            $table->enum('decision_res', ['remis_en_stock', 'a_reparer', 'rebut'])->nullable();
            
            $table->timestamps();
            
            $table->index('materiel_id');
            $table->index('employee_id');
            $table->index('date_attribution');
            $table->index('date_restitution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributions');
    }
};
