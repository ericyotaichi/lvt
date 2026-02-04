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
        Schema::create('tech_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title_zh')->nullable();
            $table->string('title_en')->nullable();
            $table->longText('content_zh')->nullable();
            $table->longText('content_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tech_pages');
    }
};
