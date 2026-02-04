<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        // 使用 DB 查询检查列是否存在（更可靠）
        $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'");
        $columnNames = array_column($columns, 'COLUMN_NAME');

        Schema::table('products', function (Blueprint $table) use ($columnNames) {
            // 添加类别字段（七大固定项目）
            if (!in_array('category', $columnNames)) {
                if (in_array('title', $columnNames)) {
                    $table->string('category')->nullable()->after('title');
                } else {
                    $table->string('category')->nullable();
                }
            }
            // 确保 content 字段存在（用于富文本内容）
            if (!in_array('content', $columnNames)) {
                if (in_array('description_en', $columnNames)) {
                    $table->longText('content')->nullable()->after('description_en');
                } else {
                    $table->longText('content')->nullable();
                }
            }
            // 确保 content_en 字段存在（用于富文本内容英文版）
            if (!in_array('content_en', $columnNames)) {
                if (in_array('content', $columnNames)) {
                    $table->longText('content_en')->nullable()->after('content');
                } else {
                    $table->longText('content_en')->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'");
        $columnNames = array_column($columns, 'COLUMN_NAME');

        Schema::table('products', function (Blueprint $table) use ($columnNames) {
            if (in_array('category', $columnNames)) {
                $table->dropColumn('category');
            }
            // 注意：不删除 content 和 content_en 字段，因为它们可能在其他地方被使用
        });
    }
};
