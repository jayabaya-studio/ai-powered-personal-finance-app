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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom setelah kolom 'password' untuk kerapian
            $table->string('profile_photo_path', 2048)->nullable()->after('password');
            $table->string('timezone')->nullable()->after('profile_photo_path');
            $table->tinyInteger('payday')->unsigned()->nullable()->after('timezone');
            $table->string('income_source')->nullable()->after('payday');
            $table->string('location')->nullable()->after('income_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo_path',
                'timezone',
                'payday',
                'income_source',
                'location',
            ]);
        });
    }
};
