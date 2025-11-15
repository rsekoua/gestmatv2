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
            
            $table->foreignUuid('service_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('responsable_service')->nullable();

            $table->foreignUuid('employee_id')->nullable()->constrained()->restrictOnDelete();
            $table->date('date_attribution');
            $table->date('date_restitution')->nullable();
            $table->string('numero_decharge_att', 50)->unique()->nullable();
            $table->string('numero_decharge_res', 50)->unique()->nullable();


            // Modifier employee_id pour le rendre nullable
           // $table->foreignUuid('employee_id')->nullable()->change();

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
            $table->index(['materiel_id', 'date_restitution'], 'attributions_materiel_active_index');

            // Index composite pour les attributions d'un employé
            $table->index(['employee_id', 'date_restitution'], 'attributions_employee_active_index');

            // Index composite pour les attributions d'un service
            $table->index(['service_id', 'date_restitution'], 'attributions_service_active_index');
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
