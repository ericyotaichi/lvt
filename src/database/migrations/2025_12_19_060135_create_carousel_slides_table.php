<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carousel_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // 中文标题
            $table->string('title_en')->nullable(); // 英文标题
            $table->text('description')->nullable(); // 中文描述
            $table->text('description_en')->nullable(); // 英文描述
            $table->string('image_url')->nullable(); // 图片URL
            $table->string('link_url')->nullable(); // 链接URL（可选）
            $table->string('link_text')->nullable(); // 链接文字（中文）
            $table->string('link_text_en')->nullable(); // 链接文字（英文）
            $table->integer('sort')->default(0); // 排序
            $table->boolean('status')->default(true); // 是否启用
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carousel_slides');
    }
};
