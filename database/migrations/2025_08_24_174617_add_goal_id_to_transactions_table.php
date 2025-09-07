<?php
// File: database/migrations/<timestamp>_add_goal_id_to_transactions_table.php

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
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan kolom goal_id sebagai foreign key, nullable
            $table->foreignId('goal_id')->nullable()->constrained()->onDelete('set null')->after('transfer_to_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropConstrainedForeignId('goal_id');
            // Kemudian hapus kolom
            $table->dropColumn('goal_id');
        });
    }
};

