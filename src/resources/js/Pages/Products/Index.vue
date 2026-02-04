<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  items: { type: Array, default: () => [] }
})
const { t } = useTranslations()

const getApplicationAnchor = (productId) => productId ? `/applications#application-category-${productId}` : '/applications#application-list'
</script>
<script>import AppLayout from '@/Layouts/AppLayout.vue'; export default { layout: AppLayout }</script>

<template>
  <Head :title="t('products.title')" />
  <h1 class="text-3xl font-bold">{{ t('products.title') }}</h1>
  <p class="mt-2 text-gray-600">{{ t('products.description') }}</p>

  <div v-if="props.items.length" class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <article
      v-for="item in props.items"
      :key="item.id"
      class="rounded-2xl border bg-white p-5 hover:shadow-md transition-shadow flex flex-col"
    >
      <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
        <img
          v-if="item.cover_url"
          :src="item.cover_url"
          :alt="item.title"
          class="w-full h-full object-cover"
        />
        <div v-else class="w-full h-full grid place-items-center text-gray-400 text-sm">
          {{ t('no_image_uploaded') }}
        </div>
      </div>

      <h2 class="text-xl font-semibold mb-2">{{ item.title }}</h2>

      <p v-if="item.summary" class="text-gray-600 text-sm mb-3 line-clamp-3">
        {{ item.summary }}
      </p>
      <p v-else-if="item.content" class="text-gray-600 text-sm mb-3 line-clamp-3" v-html="item.content"></p>
      <p v-else class="text-gray-400 text-sm mb-3 italic">{{ t('no_description') }}</p>

      <div class="mt-auto flex flex-wrap gap-3">
        <Link
          :href="getApplicationAnchor(item.id)"
          class="inline-flex items-center gap-1 text-sm font-medium text-blue-700 hover:text-blue-900 hover:underline"
        >
          {{ t('learn_more') }}
          <span aria-hidden="true">→</span>
        </Link>
        <Link
          v-if="item.slug"
          :href="route('products.show', item.slug)"
          class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700"
        >
          {{ t('view_product') }}
        </Link>
      </div>
    </article>
  </div>

  <div v-else class="mt-8 rounded-2xl border border-dashed bg-white/60 p-8 text-center text-gray-500">
    {{ t('no_products') }}
  </div>
</template>
