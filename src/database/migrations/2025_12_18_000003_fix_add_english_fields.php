<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Products 表添加英文字段
        if (Schema::hasTable('products')) {
            $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'");
            $columnNames = array_column($columns, 'COLUMN_NAME');
            
            Schema::table('products', function (Blueprint $table) use ($columnNames) {
                if (!in_array('title_en', $columnNames)) {
                    if (in_array('title', $columnNames)) {
                        $table->string('title_en')->nullable()->after('title');
                    } else {
                        $table->string('title_en')->nullable();
                    }
                }
                if (!in_array('summary_en', $columnNames)) {
                    if (in_array('summary', $columnNames)) {
                        $table->string('summary_en')->nullable()->after('summary');
                    } else {
                        $table->string('summary_en')->nullable();
                    }
                }
                if (!in_array('description_en', $columnNames)) {
                    if (in_array('description', $columnNames)) {
                        $table->longText('description_en')->nullable()->after('description');
                    } else {
                        $table->longText('description_en')->nullable();
                    }
                }
            });
        }

        // Applications 表添加英文字段
        if (Schema::hasTable('applications')) {
            $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'applications'");
            $columnNames = array_column($columns, 'COLUMN_NAME');
            
            Schema::table('applications', function (Blueprint $table) use ($columnNames) {
                if (!in_array('title_en', $columnNames)) {
                    if (in_array('title', $columnNames)) {
                        $table->string('title_en')->nullable()->after('title');
                    } else {
                        $table->string('title_en')->nullable();
                    }
                }
                if (!in_array('excerpt_en', $columnNames)) {
                    if (in_array('excerpt', $columnNames)) {
                        $table->string('excerpt_en')->nullable()->after('excerpt');
                    } else {
                        $table->string('excerpt_en')->nullable();
                    }
                }
                if (!in_array('content_en', $columnNames)) {
                    if (in_array('content', $columnNames)) {
                        $table->longText('content_en')->nullable()->after('content');
                    } else {
                        $table->longText('content_en')->nullable();
                    }
                }
            });
        }

        // Cases 表添加英文字段
        if (Schema::hasTable('cases')) {
            $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cases'");
            $columnNames = array_column($columns, 'COLUMN_NAME');
            
            Schema::table('cases', function (Blueprint $table) use ($columnNames) {
                if (!in_array('title_en', $columnNames)) {
                    if (in_array('title', $columnNames)) {
                        $table->string('title_en')->nullable()->after('title');
                    } else {
                        $table->string('title_en')->nullable();
                    }
                }
                if (!in_array('excerpt_en', $columnNames)) {
                    if (in_array('excerpt', $columnNames)) {
                        $table->string('excerpt_en')->nullable()->after('excerpt');
                    } else {
                        $table->string('excerpt_en')->nullable();
                    }
                }
                if (!in_array('content_en', $columnNames)) {
                    if (in_array('content', $columnNames)) {
                        $table->longText('content_en')->nullable()->after('content');
                    } else {
                        $table->longText('content_en')->nullable();
                    }
                }
                if (!in_array('customer_en', $columnNames)) {
                    if (in_array('customer', $columnNames)) {
                        $table->string('customer_en')->nullable()->after('customer');
                    } else {
                        $table->string('customer_en')->nullable();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // 不删除字段，避免数据丢失
    }
};

