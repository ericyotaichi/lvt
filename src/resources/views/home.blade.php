@extends('layouts.app')

@section('content')
    {{-- 輪播 --}}

    <swiper class="mySwiper">
      <swiper-slide>Slide 1</swiper-slide>
      <swiper-slide>Slide 2</swiper-slide><swiper-slide>Slide 3</swiper-slide>
      <swiper-slide>Slide 4</swiper-slide><swiper-slide>Slide 5</swiper-slide>
      <swiper-slide>Slide 6</swiper-slide><swiper-slide>Slide 7</swiper-slide>
      <swiper-slide>Slide 8</swiper-slide><swiper-slide>Slide 9</swiper-slide>
    </swiper>



    {{-- Hero 區：綠/藍 漸層、節能環保訴求 --}}
    <section class="mt-8 md:mt-12 rounded-3xl overflow-hidden bg-gradient-to-br from-green-50 to-blue-50 border ">
        <div class="px-6 md:px-10 py-12 md:py-16 grid md:grid-cols-2 items-center gap-8">
            <div>
                <p class="text-green-700 text-sm font-semibold mb-2">Sustainable • Efficient • Smart</p>
                <h1 class="text-3xl md:text-5xl font-bold leading-tight">
                    以核心技術，打造節能環保的智慧解決方案
                </h1>
                <p class="mt-4 text-gray-600">
                    結合核心技術與產業應用，提供完整的產品與服務，協助企業加速綠色轉型。
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="/products" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700">探索產品與服務</a>
             
                </div>
            </div>
            <div class="relative">
                <div class="aspect-[4/3] rounded-2xl bg-white/70 border shadow-sm"></div>
                <div class="absolute -bottom-4 -left-4 w-28 h-28 rounded-2xl bg-green-100 blur"></div>
                <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-blue-100 blur"></div>
            </div>
        </div>
    </section>

    {{-- 三大價值主張 --}}
    <section class="mt-14 grid md:grid-cols-3 gap-6">
        @php
            $highlights = [
                ['title' => '節能減碳', 'desc' => '以數據驅動的能效最佳化，降低能耗與成本'],
                ['title' => '穩定可靠', 'desc' => '企業級架構與監控，確保長期運行的穩定性'],
                ['title' => '快速導入', 'desc' => '模組化方案與專業顧問，縮短導入時程'],
            ];
        @endphp
        @foreach ($highlights as $h)
            <article class="rounded-2xl border bg-white p-6 hover:shadow-sm">
                <h3 class="text-lg font-semibold">{{ $h['title'] }}</h3>
                <p class="mt-2 text-gray-600">{{ $h['desc'] }}</p>
            </article>
        @endforeach
    </section>

    {{-- 產品與服務（7 項，卡片樣式） --}}
    <section class="mt-14">
        <h2 class="text-2xl font-bold">產品與服務內容</h2>
        <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $products = [
                    [
                        'title' => '方案 A',
                        'desc' => '以核心技術為基底的標準化解決方案',
                        'link' => '/products/solution-a',
                    ],
                    ['title' => '方案 B', 'desc' => '快速導入、可擴充的應用模組', 'link' => '/products/solution-b'],
                    ['title' => '顧問服務', 'desc' => '需求盤點、規劃與導入陪跑', 'link' => '/products/consulting'],
                    ['title' => '維運監控', 'desc' => '可視化儀表板與告警機制', 'link' => '/products/operation'],
                    ['title' => '資料平台', 'desc' => '資料收斂、清洗與 API 服務', 'link' => '/products/data-platform'],
                    ['title' => 'AI/ML', 'desc' => '預測與最佳化模型導入', 'link' => '/products/ai-ml'],
                    ['title' => '教育訓練', 'desc' => '上線後的培訓與內化', 'link' => '/products/training'],
                ];
            @endphp
            @foreach ($products as $p)
                <article class="rounded-2xl border bg-white p-5 hover:shadow-sm">
                    <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4"></div>
                    <h3 class="text-lg font-semibold">{{ $p['title'] }}</h3>
                    <p class="text-gray-600 mt-1">{{ $p['desc'] }}</p>
                    <a href="{{ $p['link'] }}" class="inline-block mt-3 text-blue-700 hover:underline">了解更多 →</a>
                </article>
            @endforeach
        </div>
    </section>

    {{-- 應用場域 / 案例（需填表單後解鎖的預告） --}}
    <section class="mt-14 grid md:grid-cols-2 gap-6">
        <article class="rounded-2xl border bg-white p-6">
            <h3 class="text-xl font-semibold">應用場域</h3>
            <p class="text-gray-600 mt-2">完整的產業場域案例，完成表單後即可瀏覽。</p>
            <a href="/applications" class="inline-block mt-3 px-4 py-2 rounded-xl border hover:bg-white">前往應用場域</a>
        </article>
        {{-- <article class="rounded-2xl border bg-white p-6">
            <h3 class="text-xl font-semibold">案例說明</h3>
            <p class="text-gray-600 mt-2">實際導入案例與效益說明，完成表單後即可瀏覽。</p>
            <a href="/cases" class="inline-block mt-3 px-4 py-2 rounded-xl border hover:bg-white">前往案例說明</a>
        </article> --}}
    </section>

    {{-- CTA：詢問與聯繫（表單入口） --}}
    <section class="mt-16 mb-20 rounded-3xl overflow-hidden border bg-gradient-to-r from-green-600 to-blue-600 text-white">
        <div class="px-6 md:px-10 py-10 md:py-12 grid md:grid-cols-2 items-center gap-6">
            <div>
                <h3 class="text-2xl font-semibold">有需求或想進一步了解？</h3>
                <p class="mt-2 text-white/90">填寫聯繫表單，我們將依您的主題與需求回覆。</p>
            </div>
            <div class="text-right">
                <a href="/lead"
                    class="inline-block px-5 py-2.5 rounded-xl bg-white text-green-700 font-medium hover:bg-gray-100">前往表單</a>
            </div>
        </div>
    </section>
@endsection
