<script setup>
import { Head, Link } from '@inertiajs/vue3'
import HeroCarousel from '@/Components/HeroCarousel.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'
import hero  from '@/assets/images/hero.jpg'
import home1 from '@/assets/images/1.jpg'
import home2 from '@/assets/images/2.jpg'
import home3 from '@/assets/images/3.jpg'
import home4 from '@/assets/images/4.jpg'

// ✅ 接收後端注入的最新產品（已在 Controller 查好）
const props = defineProps({
  latestProducts: { type: Array, default: () => [] }
})

const { t } = useTranslations()
</script>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head :title="t('title')" />

  <!-- ❖ Full-bleed 外框：讓內容突破容器、滿版顯示 -->
  <div class="relative w-screen left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]">
    <!-- Hero 輪播（滿版） -->
    <HeroCarousel />
  </div>

  <!-- Hero 區 -->
  <section class="rounded-3xl overflow-hidden bg-gradient-to-br from-green-50 to-blue-50 border">
    <div class="px-6 md:px-10 py-12 md:py-16 grid md:grid-cols-2 items-center gap-8">
      <div>
        <p class="text-green-700 text-sm font-semibold mb-2">{{ t('tagline') }}</p>
        <h1 class="text-3xl md:text-5xl font-bold leading-tight">{{ t('company_name') }}</h1>
        <p class="mt-4 text-gray-600">
          {{ t('company_slogan') }}
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <Link href="/about" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700">{{ t('about_us') }}</Link>
          <Link href="/tech" class="px-4 py-2 rounded-xl border hover:bg-white">{{ t('core_technology') }}</Link>
        </div>
      </div>
      <div class="relative">
        <div class="aspect-[4/3] rounded-2xl bg-white/70 bord er shadow-sm overflow-hidden">
          <img :src="hero" alt="banner" class="w-full h-full object-cover" />
        </div>
        <div class="absolute -bottom-4 -left-4 w-28 h-28 rounded-2xl bg-green-100/70 blur"></div>
        <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-blue-100/70 blur"></div>
      </div>
    </div>
  </section>

  <!-- 核心技術 -->
  <section class="mt-14">
    <div class="flex items-end justify-between">
      <h2 class="text-2xl font-bold">{{ t('core_technology') }}</h2>
      <Link href="/tech" class="text-sm text-blue-700 hover:underline">{{ t('view_all') }}</Link>
    </div>

    <!-- 三大價值主張 -->
    <div class="mt-6 grid md:grid-cols-3 gap-6">
    <article class="rounded-2xl border bg-white p-6 hover:shadow-sm">
      <img :src="home1" alt="banner" class="w-full rounded-2xl" />
      <h3 class="mt-4 text-lg font-semibold">{{ t('energy_saving') }}</h3>
      <p class="mt-2 text-gray-600">{{ t('energy_saving_desc') }}</p>
    </article>
    <article class="rounded-2xl border bg-white p-6 hover:shadow-sm">
      <img :src="home2" alt="banner" class="w-full rounded-2xl" />
      <h3 class="mt-4 text-lg font-semibold">{{ t('stable_reliable') }}</h3>
      <p class="mt-2 text-gray-600">{{ t('stable_reliable_desc') }}</p>
    </article>
    <article class="rounded-2xl border bg-white p-6 hover:shadow-sm">
      <img :src="home3" alt="banner" class="w-full rounded-2xl" />
      <h3 class="mt-4 text-lg font-semibold">{{ t('quick_deployment') }}</h3>
      <p class="mt-2 text-gray-600">{{ t('quick_deployment_desc') }}</p>
    </article>
    </div>
  </section>

  <!-- 產品與服務（✅ 改為吃 props.latestProducts，其他樣式不動） -->
  <section class="mt-14">
    <div class="flex items-end justify-between">
      <h2 class="text-2xl font-bold">{{ t('products_services') }}</h2>
      <Link href="/products" class="text-sm text-blue-700 hover:underline">{{ t('view_all') }}</Link>
    </div>

    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <article
        v-for="p in props.latestProducts"
        :key="p.id"
        class="rounded-2xl border bg-white p-5 hover:shadow-sm"
      >
        <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
          <img
            v-if="p.cover_url"
            :src="p.cover_url"
            :alt="p.title"
            class="w-full h-full object-cover"
          />
          <!-- 沒封面就顯示灰底佔位，不改原樣式 -->
        </div>
        <h3 class="text-lg font-semibold">{{ p.title }}</h3>
        <p class="text-gray-600 mt-1">
          {{ p.summary || '—' }}
        </p>
        <Link :href="route('applications.index')" class="inline-block mt-3 text-blue-700 hover:underline">
          {{ t('learn_more') }}
        </Link>
      </article>

      <!-- 若資料不足 3 筆，也可以留空位或什麼都不顯示；這裡不額外生卡片，維持你原設計 -->
    </div>
  </section>

  <!-- 應用場域 & 案例 -->
  <section class="mt-14 grid md:grid-cols-2 gap-6">
    <article class="rounded-2xl border bg-white p-6">
      <h3 class="text-xl font-semibold">{{ t('applications') }}</h3>
      <p class="mt-2 text-gray-600">{{ t('applications_desc') }}</p>
      <Link href="/applications" class="inline-block mt-3 px-4 py-2 rounded-xl border hover:bg-white">{{ t('go_to_applications') }}</Link>
    </article>
    <article class="rounded-2xl border bg-white p-6">
      <h3 class="text-xl font-semibold">{{ t('cases') }}</h3>
      <p class="mt-2 text-gray-600">{{ t('cases_desc') }}</p>
      <Link href="/cases" class="inline-block mt-3 px-4 py-2 rounded-xl border hover:bg-white">{{ t('go_to_cases') }}</Link>
    </article>
  </section>

  <!-- CTA -->
  <section class="mt-16 mb-20 rounded-3xl overflow-hidden border bg-gradient-to-r from-green-600 to-blue-600 text-white">
    <div class="px-6 md:px-10 py-10 md:py-12 grid md:grid-cols-2 items-center gap-6">
      <div>
        <h3 class="text-2xl font-semibold">{{ t('cta_title') }}</h3>
        <p class="mt-2 text-white/90">{{ t('cta_desc') }}</p>
      </div>
      <div class="text-right">
        <Link href="/lead" class="inline-block px-5 py-2.5 rounded-xl bg-white text-green-700 font-medium hover:bg-gray-100">
          {{ t('go_to_form') }}
        </Link>
      </div>
    </div>
  </section>
</template>
