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
                // current_family_id: Menunjukkan FamilySpace yang sedang aktif dilihat/dikelola user
                $table->foreignId('current_family_id')->nullable()->constrained('family_spaces')->onDelete('set null');
                // role: Peran user dalam FamilySpace (admin/member). Default 'member'.
                $table->string('role')->default('member')->after('password'); // After password for logical placement
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['current_family_id']);
                $table->dropColumn('current_family_id');
                $table->dropColumn('role');
            });
        }
    };
    