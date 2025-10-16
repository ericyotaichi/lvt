<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Products 表添加英文字段
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'title_en')) {
                if (Schema::hasColumn('products', 'title')) {
                    $table->string('title_en')->nullable()->after('title');
                } else {
                    $table->string('title_en')->nullable();
                }
            }
            if (!Schema::hasColumn('products', 'summary_en')) {
                if (Schema::hasColumn('products', 'summary')) {
                    $table->string('summary_en')->nullable()->after('summary');
                } else {
                    $table->string('summary_en')->nullable();
                }
            }
            if (!Schema::hasColumn('products', 'description_en')) {
                if (Schema::hasColumn('products', 'description')) {
                    $table->longText('description_en')->nullable()->after('description');
                } else {
                    $table->longText('description_en')->nullable();
                }
            }
        });
        }

        // Applications 表添加英文字段
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'title_en')) {
                if (Schema::hasColumn('applications', 'title')) {
                    $table->string('title_en')->nullable()->after('title');
                } else {
                    $table->string('title_en')->nullable();
                }
            }
            if (!Schema::hasColumn('applications', 'excerpt_en')) {
                if (Schema::hasColumn('applications', 'excerpt')) {
                    $table->string('excerpt_en')->nullable()->after('excerpt');
                } else {
                    $table->string('excerpt_en')->nullable();
                }
            }
            if (!Schema::hasColumn('applications', 'content_en')) {
                if (Schema::hasColumn('applications', 'content')) {
                    $table->longText('content_en')->nullable()->after('content');
                } else {
                    $table->longText('content_en')->nullable();
                }
            }
        });
        }

        // Cases 表添加英文字段
        if (Schema::hasTable('cases')) {
            Schema::table('cases', function (Blueprint $table) {
            if (!Schema::hasColumn('cases', 'title_en')) {
                if (Schema::hasColumn('cases', 'title')) {
                    $table->string('title_en')->nullable()->after('title');
                } else {
                    $table->string('title_en')->nullable();
                }
            }
            if (!Schema::hasColumn('cases', 'excerpt_en')) {
                if (Schema::hasColumn('cases', 'excerpt')) {
                    $table->string('excerpt_en')->nullable()->after('excerpt');
                } else {
                    $table->string('excerpt_en')->nullable();
                }
            }
            if (!Schema::hasColumn('cases', 'content_en')) {
                if (Schema::hasColumn('cases', 'content')) {
                    $table->longText('content_en')->nullable()->after('content');
                } else {
                    $table->longText('content_en')->nullable();
                }
            }
            if (!Schema::hasColumn('cases', 'customer_en')) {
                if (Schema::hasColumn('cases', 'customer')) {
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'summary_en', 'description_en']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'excerpt_en', 'content_en']);
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'excerpt_en', 'content_en', 'customer_en']);
        });
    }
};

