<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  item: { type: Object, required: true }
})

const { t } = useTranslations()
</script>
<script>import AppLayout from '@/Layouts/AppLayout.vue'; export default { layout: AppLayout }</script>

<template>
  <Head :title="props.item.title || props.item.name" />
  
  <article class="rounded-2xl border bg-white p-6 mb-8">
    <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-6 overflow-hidden">
      <img v-if="props.item.cover_url" :src="props.item.cover_url" class="w-full h-full object-cover" />
    </div>
    <h1 class="text-3xl font-bold">{{ props.item.title || props.item.name }}</h1>
    <div 
      v-if="props.item.content"
      class="mt-6 prose prose-lg max-w-none"
      v-html="props.item.content"
    ></div>
    <div v-else-if="props.item.description" class="mt-3 text-gray-700 whitespace-pre-line">
      {{ props.item.description }}
    </div>
    <p v-else-if="props.item.summary" class="mt-3 text-gray-600">
      {{ props.item.summary }}
    </p>
  </article>
</template>
