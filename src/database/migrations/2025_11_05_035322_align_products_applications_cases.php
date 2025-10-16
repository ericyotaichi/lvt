// database/migrations/2025_11_05_000001_align_products_applications_cases.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // products 已存在就略過，這裡只展示補欄位的做法
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications','product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('applications','excerpt'))   $table->string('excerpt')->nullable();
            if (!Schema::hasColumn('applications','cover_url')) $table->string('cover_url')->nullable();
            if (!Schema::hasColumn('applications','status'))    $table->enum('status',['draft','published'])->default('draft');
            if (!Schema::hasColumn('applications','sort'))      $table->integer('sort')->default(0);
        });

        Schema::table('cases', function (Blueprint $table) {
            if (!Schema::hasColumn('cases','product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('cases','excerpt'))    $table->string('excerpt')->nullable();
            if (!Schema::hasColumn('cases','cover_url'))  $table->string('cover_url')->nullable();
            if (!Schema::hasColumn('cases','customer'))   $table->string('customer')->nullable();
            if (!Schema::hasColumn('cases','status'))     $table->enum('status',['draft','published'])->default('draft');
            if (!Schema::hasColumn('cases','sort'))       $table->integer('sort')->default(0);
        });
    }

    public function down(): void
    {
        // 為安全起見不回滾欄位（避免資料遺失），如需可自行 dropColumn 檢查後再刪
    }
};
