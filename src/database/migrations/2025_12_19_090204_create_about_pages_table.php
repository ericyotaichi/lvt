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
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->text('content_zh')->nullable()->comment('中文公司介紹內容');
            $table->text('content_en')->nullable()->comment('英文公司介紹內容');
            $table->string('image_url')->nullable()->comment('公司介紹圖片URL');
            $table->timestamps();
        });
        
        // 插入預設內容
        DB::table('about_pages')->insert([
            'content_zh' => '村源科技股份有限公司秉持「以人為本、健康、節能」為核心價值，致力於打造兼具永續發展與人本關懷的工程技術解決方案。我們相信，真正的技術創新，應該立足於人類的福祉、環境的永續與資源的高效利用。因此，我們不僅提供專業的工程設計與施工服務，更深入探究每一個項目的基礎核心問題，從源頭提出有價值的解決方案，創造長遠效益。

我們的服務從前期規劃、設計分析、能源診斷，到施工監造與系統優化，皆以「創造價值」為導向，協助客戶打造高效、健康且可持續的空間與系統環境。

在現今快速變動的科技與產業環境中，村源科技持續投入研發與創新，結合數據監控、智能控制與節能模組等前瞻技術，協助各產業邁向低碳與智慧化的營運模式。無論是工業廠房、辦公大樓、特殊用途空間，或公共基礎設施，我們皆能提供客製化的全方位工程解決方案。',
            'content_en' => 'Village Source Technology Co., Ltd. adheres to the core values of "people-oriented, health, and energy saving" and is committed to creating engineering technology solutions that combine sustainable development with humanistic care. We believe that true technological innovation should be based on human well-being, environmental sustainability, and efficient resource utilization. Therefore, we not only provide professional engineering design and construction services but also deeply explore the fundamental core issues of each project, proposing valuable solutions from the source to create long-term benefits.

Our services, from preliminary planning, design analysis, energy diagnosis to construction supervision and system optimization, are all value-oriented, helping customers create efficient, healthy, and sustainable space and system environments.

In today\'s rapidly changing technology and industry environment, Village Source Technology continues to invest in R&D and innovation, combining forward-looking technologies such as data monitoring, intelligent control, and energy-saving modules to help various industries move towards low-carbon and intelligent operation models. Whether it is industrial plants, office buildings, special-purpose spaces, or public infrastructure, we can provide customized comprehensive engineering solutions.',
            'image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};
