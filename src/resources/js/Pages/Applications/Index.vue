<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'
import { computed } from 'vue'

const props = defineProps({
  applicationsByCategory: { type: Object, default: () => ({}) },
  categories: { type: Array, default: () => [] }
})
const { t } = useTranslations()

const normalizedCategories = computed(() => {
  return props.categories.map((category) => {
    const key = String(category.id)
    const raw = props.applicationsByCategory[key] || {}
    const items = Array.isArray(raw.items) ? raw.items : []
    return {
      key,
      title: raw.categoryLabel || category.title,
      items,
      empty: raw.empty ?? items.length === 0,
    }
  })
})

const getAnchorId = (key) => `application-category-${key}`
</script>
<script>import AppLayout from '@/Layouts/AppLayout.vue'; export default { layout: AppLayout }</script>

<template>
  <Head :title="t('applications_section.title')" />
  <section id="application-list">
    <h1 class="text-3xl font-bold">{{ t('applications_section.title') }}</h1>
    <p class="mt-2 text-gray-600">{{ t('applications_section.description') }}</p>
  </section>

  <div v-if="normalizedCategories.length" class="mt-8 space-y-12">
    <section
      v-for="(category, index) in normalizedCategories"
      :key="category.key"
      :id="getAnchorId(category.key)"
      class="scroll-mt-28 space-y-4"
    >
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <h2 class="text-2xl font-bold">
          {{ index + 1 }}. {{ category.title }}
        </h2>
        <p class="text-sm text-gray-500">
          {{ t('applications_section.description') }}
        </p>
      </div>

      <div v-if="category.items.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="item in category.items"
          :key="item.id"
          class="rounded-2xl border bg-white p-5 hover:shadow-md transition-shadow"
        >
          <div class="aspect-[16/9] rounded-xl bg-gray-100 mb-4 overflow-hidden">
            <img v-if="item.cover_url" :src="item.cover_url" :alt="item.title" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full grid place-items-center text-gray-400 text-sm">
              {{ t('no_image_uploaded') }}
            </div>
          </div>
          <h3 class="text-lg font-semibold">{{ item.title }}</h3>
          <p class="text-gray-600 mt-2 line-clamp-3">{{ item.excerpt }}</p>
          <Link
            :href="route('applications.show', item.slug)"
            class="inline-flex items-center gap-1 mt-4 text-blue-700 hover:text-blue-900 hover:underline text-sm font-medium"
          >
            {{ t('learn_more') }}
            <span aria-hidden="true">→</span>
          </Link>
        </article>
      </div>
      <div v-else class="rounded-2xl border border-dashed bg-white/60 p-6 text-gray-500 text-sm">
        {{ t('no_applications') }}
      </div>
    </section>
  </div>
  <div v-else class="mt-8 rounded-2xl border border-dashed bg-white/60 p-8 text-center text-gray-500">
    {{ t('no_applications_category') }}
  </div>
</template>
