<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_excerpt_cover_to_applications_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'excerpt')) {
                $table->string('excerpt')->nullable()->after('title');
            }
            if (!Schema::hasColumn('applications', 'cover_url')) {
                $table->string('cover_url')->nullable()->after('excerpt');
            }
        });
    }
    public function down(): void {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'cover_url')) {
                $table->dropColumn('cover_url');
            }
            if (Schema::hasColumn('applications', 'excerpt')) {
                $table->dropColumn('excerpt');
            }
        });
    }
};
