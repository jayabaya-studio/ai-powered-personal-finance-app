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
            Schema::table('accounts', function (Blueprint $table) {
                // family_space_id: Foreign key ke family_spaces.id jika akun ini adalah joint account
                $table->foreignId('family_space_id')->nullable()->constrained('family_spaces')->onDelete('set null');
                // is_joint: Flag boolean untuk menandakan apakah akun ini adalah joint account
                $table->boolean('is_joint')->default(false);

                // Tambahkan index untuk kolom baru agar query lebih cepat
                $table->index(['family_space_id', 'is_joint']);
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropIndex(['family_space_id', 'is_joint']); // Hapus index dulu
                $table->dropForeign(['family_space_id']);
                $table->dropColumn('family_space_id');
                $table->dropColumn('is_joint');
            });
        }
    };
    