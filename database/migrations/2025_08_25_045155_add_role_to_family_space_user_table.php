<?php
// File: database/migrations/<timestamp>_add_role_to_family_space_user_table.php

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
        Schema::table('family_space_user', function (Blueprint $table) {
            // Menambahkan kolom 'role' dengan nilai default 'member'
            // Setelah kolom 'user_id' untuk konsistensi
            $table->string('role')->default('member')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_space_user', function (Blueprint $table) {
            // Drop kolom 'role' jika migrasi di-rollback
            $table->dropColumn('role');
        });
    }
};

