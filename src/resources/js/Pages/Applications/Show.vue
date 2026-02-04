<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  item: { type: Object, required: true },
  relatedCases: { type: Array, default: () => [] },
  relatedProducts: { type: Array, default: () => [] }
})

const { t } = useTranslations()
</script>
<script>import AppLayout from '@/Layouts/AppLayout.vue'; export default { layout: AppLayout }</script>

<template>
  <Head :title="props.item.title" />
  
  <article class="rounded-2xl border bg-white p-6 mb-8">
    <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-6 overflow-hidden">
      <img v-if="props.item.cover_url" :src="props.item.cover_url" class="w-full h-full object-cover" />
    </div>
    <h1 class="text-3xl font-bold">{{ props.item.title }}</h1>
    <div class="mt-3 text-gray-700 leading-7">
      <div v-if="props.item.content" v-html="props.item.content"></div>
      <p v-else-if="props.item.excerpt" class="whitespace-pre-line">
        {{ props.item.excerpt }}
      </p>
      <p v-else class="text-gray-500 text-sm italic">
        {{ t('no_content') }}
      </p>
    </div>
  </article>

  <!-- 相關案例說明 -->
  <section v-if="relatedCases && relatedCases.length > 0" class="mb-8">
    <h2 class="text-2xl font-bold mb-4">{{ t('applications_section.related_cases') }}</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <article v-for="caseItem in relatedCases" :key="caseItem.id" class="rounded-2xl border bg-white p-5 hover:shadow-lg transition-shadow">
        <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
          <img v-if="caseItem.cover_url" :src="caseItem.cover_url" class="w-full h-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold mb-2">{{ caseItem.title }}</h3>
        <p v-if="caseItem.customer" class="text-sm text-gray-500 mb-2">{{ t('cases_section.customer') }}：{{ caseItem.customer }}</p>
        <p v-if="caseItem.excerpt" class="text-gray-600 text-sm line-clamp-2 mb-3">{{ caseItem.excerpt }}</p>
        <Link 
          :href="route('cases.show', caseItem.slug)" 
          class="inline-block text-blue-700 hover:text-blue-900 font-medium hover:underline"
        >
          {{ t('learn_more') }}
        </Link>
      </article>
    </div>
  </section>

  <!-- 相關產品科技 -->
  <section v-if="relatedProducts && relatedProducts.length > 0" class="mb-8">
    <h2 class="text-2xl font-bold mb-4">{{ t('applications_section.related_products') }}</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <article v-for="product in relatedProducts" :key="product.id" class="rounded-2xl border bg-white p-5 hover:shadow-lg transition-shadow">
        <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
          <img v-if="product.cover_url" :src="product.cover_url" class="w-full h-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold mb-2">{{ product.title }}</h3>
        <p v-if="product.summary" class="text-gray-600 text-sm line-clamp-2 mb-3">{{ product.summary }}</p>
        <Link 
          :href="route('products.show', product.slug)" 
          class="inline-block text-blue-700 hover:text-blue-900 font-medium hover:underline"
        >
          {{ t('learn_more') }}
        </Link>
      </article>
    </div>
  </section>
</template>
