<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'summary')) {
                $table->string('summary')->nullable()->after('title');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->longText('description')->nullable()->after('summary');
            }
            if (!Schema::hasColumn('products', 'cover_url')) {
                $table->string('cover_url')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['cover_url','description','summary'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
