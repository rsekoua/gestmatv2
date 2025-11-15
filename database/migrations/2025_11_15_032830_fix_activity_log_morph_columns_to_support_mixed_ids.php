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
        Schema::table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table) {
            // Drop existing indexes first
            $table->dropIndex('subject');
            $table->dropIndex('causer');

            // Change UUID columns to string to support both UUID and bigint IDs
            $table->string('subject_id')->nullable()->change();
            $table->string('causer_id')->nullable()->change();

            // Recreate indexes
            $table->index(['subject_type', 'subject_id'], 'subject');
            $table->index(['causer_type', 'causer_id'], 'causer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('subject');
            $table->dropIndex('causer');

            // Revert back to UUID columns
            $table->uuid('subject_id')->nullable()->change();
            $table->uuid('causer_id')->nullable()->change();

            // Recreate indexes
            $table->index(['subject_type', 'subject_id'], 'subject');
            $table->index(['causer_type', 'causer_id'], 'causer');
        });
    }
};
