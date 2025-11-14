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
        Schema::table('attributions', function (Blueprint $table) {
            $table->foreignUuid('service_id')
                ->nullable()
                ->after('employee_id')
                ->constrained()
                ->restrictOnDelete();

            // Modifier employee_id pour le rendre nullable
            $table->foreignUuid('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributions', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');

            // Restaurer employee_id comme non-nullable
            $table->foreignUuid('employee_id')->nullable(false)->change();
        });
    }
};
