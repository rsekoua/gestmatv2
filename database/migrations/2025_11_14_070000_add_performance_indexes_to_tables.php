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
        // Indexes pour la table materiels
        Schema::table('materiels', function (Blueprint $table) {
            // Index simple pour filtrage par statut (très fréquent)
            $table->index('statut', 'materiels_statut_index');

            // Index composite pour filtres combinés statut + type
            $table->index(['statut', 'materiel_type_id'], 'materiels_statut_type_index');

            // Index pour recherche par état physique
            $table->index('etat_physique', 'materiels_etat_physique_index');
        });

        // Indexes pour la table employees
        Schema::table('employees', function (Blueprint $table) {
            // Index composite service + email pour recherches combinées
            $table->index(['service_id', 'email'], 'employees_service_email_index');
        });

        // Indexes pour la table attributions (table la plus sollicitée)
        Schema::table('attributions', function (Blueprint $table) {
            // Index pour date_restitution (WHERE IS NULL très fréquent)
            $table->index('date_restitution', 'attributions_date_restitution_index');

            // Index composite pour trouver l'attribution active d'un matériel
            $table->index(['materiel_id', 'date_restitution'], 'attributions_materiel_active_index');

            // Index composite pour les attributions d'un employé
            $table->index(['employee_id', 'date_restitution'], 'attributions_employee_active_index');

            // Index composite pour les attributions d'un service
            $table->index(['service_id', 'date_restitution'], 'attributions_service_active_index');

            // Index pour tri par date d'attribution
            $table->index('date_attribution', 'attributions_date_attribution_index');
        });

        // Indexes pour la table services
        Schema::table('services', function (Blueprint $table) {
            // Index pour recherche par code (si pas déjà unique)
            $table->index('code', 'services_code_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materiels', function (Blueprint $table) {
            $table->dropIndex('materiels_statut_index');
            $table->dropIndex('materiels_statut_type_index');
            $table->dropIndex('materiels_etat_physique_index');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_service_email_index');
        });

        Schema::table('attributions', function (Blueprint $table) {
            $table->dropIndex('attributions_date_restitution_index');
            $table->dropIndex('attributions_materiel_active_index');
            $table->dropIndex('attributions_employee_active_index');
            $table->dropIndex('attributions_service_active_index');
            $table->dropIndex('attributions_date_attribution_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_code_index');
        });
    }
};
