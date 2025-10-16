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
        Schema::create('footers', function (Blueprint $table) {
            $table->id();
            $table->text('content_zh')->nullable()->comment('中文頁尾內容');
            $table->text('content_en')->nullable()->comment('英文頁尾內容');
            $table->timestamps();
        });
        
        // 插入預設內容
        DB::table('footers')->insert([
            'content_zh' => '© ' . date('Y') . ' YourCompany. All rights reserved.',
            'content_en' => '© ' . date('Y') . ' YourCompany. All rights reserved.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footers');
    }
};
