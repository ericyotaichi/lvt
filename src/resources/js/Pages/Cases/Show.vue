<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  item: { type: Object, required: true },
  relatedApplications: { type: Array, default: () => [] }
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
    <p class="text-sm text-gray-500 mt-2" v-if="props.item.customer">{{ t('cases_section.customer') }}：{{ props.item.customer }}</p>
    <div class="mt-3 text-gray-700 leading-7">
      <div v-if="props.item.content" v-html="props.item.content"></div>
      <p v-else-if="props.item.excerpt" class="whitespace-pre-line">
        {{ props.item.excerpt }}
      </p>
      <p v-else class="text-gray-500 text-sm italic">
        {{ t('no_content') }}
      </p>
    </div>
    <ul v-if="props.item.metrics?.length" class="mt-6 grid sm:grid-cols-2 gap-4">
      <li v-for="m in props.item.metrics" :key="m.label" class="rounded-xl border p-4">
        <div class="text-sm text-gray-500">{{ m.label }}</div>
        <div class="text-xl font-semibold">{{ m.value }}</div>
      </li>
    </ul>
  </article>

  <!-- 相關應用場域 -->
  <section v-if="relatedApplications && relatedApplications.length > 0" class="mb-8">
    <h2 class="text-2xl font-bold mb-4">{{ t('cases_section.related_applications') }}</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <article v-for="app in relatedApplications" :key="app.id" class="rounded-2xl border bg-white p-5 hover:shadow-lg transition-shadow">
        <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
          <img v-if="app.cover_url" :src="app.cover_url" class="w-full h-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold mb-2">{{ app.title }}</h3>
        <p v-if="app.excerpt" class="text-gray-600 text-sm line-clamp-2 mb-3">{{ app.excerpt }}</p>
        <Link 
          :href="route('applications.show', app.slug)" 
          class="inline-block text-blue-700 hover:text-blue-900 font-medium hover:underline"
        >
          {{ t('learn_more') }}
        </Link>
      </article>
    </div>
  </section>
</template>
