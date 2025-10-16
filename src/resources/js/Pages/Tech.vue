<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>
<script>import AppLayout from '@/Layouts/AppLayout.vue'; export default { layout: AppLayout }</script>

<template>
  <Head title="核心技術" />
  <h1 class="text-3xl font-bold">核心技術</h1>
  <p class="mt-2 text-gray-600">以科學的方法,透過氣流的5個物理特性加上2個工程因素,確認通風系統之性能及效率.
  </p>

  <section class="mt-8 grid md:grid-cols-3 gap-6">
    <article class="rounded-2xl border bg-white p-6">
      <h3 class="text-lg font-semibold">IoT 感測與邊緣運算</h3>
      <p class="mt-2 text-gray-600">資料採集、通訊協定、閘道與邊緣 AI。</p>
    </article>
    <article class="rounded-2xl border bg-white p-6">
      <h3 class="text-lg font-semibold">資料平台與管線</h3>
      <p class="mt-2 text-gray-600">資料匯入、清洗、儲存、即時/離線分析。</p>
    </article>
    <article class="rounded-2xl border bg-white p-6">
      <h3 class="text-lg font-semibold">AI/ML 與最佳化</h3>
      <p class="mt-2 text-gray-600">預測、異常偵測、節能最佳化與決策。</p>
    </article>
    
  </section>
</template>
