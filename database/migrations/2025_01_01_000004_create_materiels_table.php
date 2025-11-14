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
        Schema::create('materiels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('materiel_type_id')->constrained('materiel_types')->onDelete('restrict');
            //            $table->string('nom', 255);
            $table->string('marque', 100)->nullable();
            $table->string('modele', 100)->nullable();
            $table->string('numero_serie', 100)->unique();
            $table->string('processor', 255)->nullable();
            $table->integer('ram_size_gb')->nullable();
            $table->integer('storage_size_gb')->nullable();
            $table->decimal('screen_size', 4, 2)->nullable();
            // $table->text('specifications')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('acquision')->nullable();
            $table->enum('statut', ['disponible', 'attribué', 'en_panne', 'en_maintenance', 'rebuté'])
                ->default('disponible');
            $table->enum('etat_physique', ['excellent', 'bon', 'moyen', 'mauvais'])
                ->default('bon');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('materiel_type_id');
            $table->index('numero_serie');
            $table->index('statut');
            // Index composite pour filtres combinés statut + type
            $table->index(['statut', 'materiel_type_id'], 'materiels_statut_type_index');
            // Index pour recherche par état physique
            $table->index('etat_physique', 'materiels_etat_physique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiels');
    }
};
