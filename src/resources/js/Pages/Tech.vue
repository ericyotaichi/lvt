<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  tech: { type: Object, default: null }
})

const page = usePage()
const locale = computed(() => page.props.locale || 'zh')
const { t } = useTranslations()
</script>
<script>import AppLayout from '@/Layouts/AppLayout.vue'; export default { layout: AppLayout }</script>

<template>
  <Head :title="tech?.title || t('core_tech')" />
  
  <article class="rounded-2xl border bg-white p-6 md:p-8">
    <h1 class="text-3xl font-bold mb-6">{{ tech?.title || t('core_tech') }}</h1>
    
    <div 
      v-if="tech?.content" 
      class="prose prose-lg max-w-none text-gray-700"
      v-html="tech.content"
    ></div>
    
    <div v-else class="text-gray-500 italic">
      {{ t('content_preparing') }}
    </div>
  </article>
</template>

<style scoped>
:deep(.prose img) {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
  margin: 1rem 0;
}

:deep(.prose h1),
:deep(.prose h2),
:deep(.prose h3) {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
}

:deep(.prose p) {
  margin-bottom: 1rem;
  line-height: 1.7;
}
</style>
