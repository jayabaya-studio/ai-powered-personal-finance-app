<?php
// File: database/migrations/<timestamp>_add_description_and_is_completed_to_goals_table.php

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
        Schema::table('goals', function (Blueprint $table) {
            // Tambahkan kolom 'description' setelah 'target_date'
            $table->text('description')->nullable()->after('target_date');
            // Tambahkan kolom 'is_completed' setelah 'description'
            $table->boolean('is_completed')->default(false)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            // Drop kolom jika migrasi di-rollback
            $table->dropColumn('is_completed');
            $table->dropColumn('description');
        });
    }
};

