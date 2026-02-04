<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({ items: { type: Array, default: () => [] } })
const { t } = useTranslations()
</script>
<script>import AppLayout from '@/Layouts/AppLayout.vue'; export default { layout: AppLayout }</script>

<template>
  <Head :title="t('cases_section.title')" />
  <h1 class="text-3xl font-bold">{{ t('cases_section.title') }}</h1>
  <p class="mt-2 text-gray-600">{{ t('cases_section.description') }}</p>

  <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <article v-for="it in props.items" :key="it.id" class="rounded-2xl border bg-white p-5 hover:shadow-sm">
      <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
        <img v-if="it.cover_url" :src="it.cover_url" class="w-full h-full object-cover" />
      </div>
      <h3 class="text-lg font-semibold">{{ it.title }}</h3>
      <p class="text-sm text-gray-500" v-if="it.customer">{{ t('cases_section.customer') }}：{{ it.customer }}</p>
      <p class="text-gray-600 mt-1 line-clamp-2">{{ it.excerpt }}</p>
      <Link :href="route('cases.show', it.slug)" class="inline-block mt-3 text-blue-700 hover:underline">
        {{ t('cases_section.view_details') }}
      </Link>
    </article>
  </div>
</template>
