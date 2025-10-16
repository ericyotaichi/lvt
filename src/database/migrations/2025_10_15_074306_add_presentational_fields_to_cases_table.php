<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cases', function (Blueprint $table) {
            if (!Schema::hasColumn('cases', 'excerpt')) {
                $table->string('excerpt')->nullable()->after('title');
            }
            if (!Schema::hasColumn('cases', 'cover_url')) {
                $table->string('cover_url')->nullable()->after('excerpt');
            }
            if (!Schema::hasColumn('cases', 'customer')) {
                $table->string('customer')->nullable()->after('cover_url');
            }
            if (!Schema::hasColumn('cases', 'metrics')) {
                $table->json('metrics')->nullable()->after('customer');
            }
        });
    }

    public function down(): void {
        Schema::table('cases', function (Blueprint $table) {
            foreach (['metrics','customer','cover_url','excerpt'] as $col) {
                if (Schema::hasColumn('cases', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
