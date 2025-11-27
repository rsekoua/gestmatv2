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
        // Add index to materiels table for search and filter optimization
        Schema::table('materiels', function (Blueprint $table) {
            $table->index('marque', 'materiels_marque_index');
            $table->index('purchase_date', 'materiels_purchase_date_index');
        });

        // Add indexes to employees table for filter optimization
        Schema::table('employees', function (Blueprint $table) {
            $table->index('emploi', 'employees_emploi_index');
            $table->index('fonction', 'employees_fonction_index');
            $table->index(['nom', 'prenom'], 'employees_nom_prenom_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materiels', function (Blueprint $table) {
            $table->dropIndex('materiels_marque_index');
            $table->dropIndex('materiels_purchase_date_index');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_emploi_index');
            $table->dropIndex('employees_fonction_index');
            $table->dropIndex('employees_nom_prenom_index');
        });
    }
};
