<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import HeroCarousel from '@/Components/HeroCarousel.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'
import hero  from '@/assets/images/hero.jpg'
import home4 from '@/assets/images/4.jpg'

// ✅ 接收後端注入的最新產品（已在 Controller 查好）
const props = defineProps({
  products: { type: Array, default: () => [] },
  tech: { type: Object, default: null },
  carouselSlides: { type: Array, default: () => [] },
})

const { t } = useTranslations()

const page = usePage()
const locale = computed(() => page.props.locale || 'zh')
const coreText = computed(() => {
  if (locale.value === 'en') {
    return {
      label: 'Core Technology',
      preparing: 'Content is being prepared...',
      cta: 'View Core Technology',
    }
  }
  return {
    label: '核心技術',
    preparing: '內容準備中...',
    cta: '查看核心技術',
  }
})

const normalize = (value) => (typeof value === 'string' ? value.trim() : '')

const coreTitle = computed(() => {
  const title = normalize(props.tech?.title)
  return title !== '' ? title : coreText.value.label
})

const hasTitle = computed(() => normalize(props.tech?.title) !== '')
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
  <section class="mt-14 rounded-3xl overflow-hidden border bg-white">
    <div class="px-6 md:px-10 py-12 md:py-16 grid md:grid-cols-2 items-center gap-8">
      <div class="order-2 md:order-1 relative">
        <div class="aspect-[4/3] rounded-2xl bg-gray-100 overflow-hidden shadow-sm">
          <img :src="home4" alt="Core technology" class="w-full h-full object-cover" />
        </div>
        <div class="absolute -top-4 -left-6 w-24 h-24 rounded-full bg-green-100/70 blur"></div>
        <div class="absolute -bottom-6 -right-8 w-28 h-28 rounded-2xl bg-blue-100/70 blur"></div>
      </div>
      <div class="order-1 md:order-2">
        <h2 class="mt-2 text-3xl md:text-4xl font-bold">{{ coreText.label }}</h2>
        <p class="mt-4 text-gray-600" v-if="hasTitle">{{ coreTitle }}</p>
        <p v-else class="mt-4 text-gray-600">{{ coreText.preparing }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
          <Link href="/tech" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700">
            {{ coreText.cta }}
          </Link>
        </div>
      </div>
    </div>
  </section>

  <!-- 產品與服務（改為依序成列顯示） -->
  <section class="mt-14">
    <div class="flex items-end justify-between mb-6">
      <h2 class="text-2xl font-bold">{{ t('products_services') }}</h2>
      <Link href="/products" class="text-sm text-blue-700 hover:underline">{{ t('view_all') }}</Link>
    </div>

    <div v-if="props.products.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <article
        v-for="product in props.products"
        :key="product.id"
        class="rounded-2xl border bg-white p-5 hover:shadow-md transition-shadow"
      >
        <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
          <img
            v-if="product.cover_url"
            :src="product.cover_url"
            :alt="product.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
            {{ t('no_image') }}
          </div>
        </div>

        <h4 class="text-lg font-semibold mb-2">{{ product.title }}</h4>

        <p v-if="product.summary" class="text-gray-600 text-sm mb-3 line-clamp-2">
          {{ product.summary }}
        </p>
        <p v-else-if="product.content" class="text-gray-600 text-sm mb-3 line-clamp-2" v-html="product.content"></p>
        <p v-else class="text-gray-400 text-sm mb-3 italic">{{ t('no_description') }}</p>

        <Link
          :href="`/applications#application-category-${product.id}`"
          class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-900 text-sm font-medium hover:underline"
        >
          {{ t('learn_more') }}
          <span aria-hidden="true">→</span>
        </Link>
      </article>
    </div>

    <div v-else class="text-gray-400 italic py-4">
      {{ t('no_content_short') }}
    </div>
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
