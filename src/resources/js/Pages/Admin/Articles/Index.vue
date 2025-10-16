<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  filters: Object,
  items: Object,
})

const page = usePage()
const { t } = useTranslations()
const locale = computed(() => page.props.locale || 'zh')

const q      = ref(props.filters?.q ?? '')
const status = ref(props.filters?.status ?? '')
const deletingId = ref(null)

const doSearch = () => {
  router.get(
    route('admin.articles.index'),
    { q: q.value || undefined, status: status.value || undefined },
    { preserveState: true, replace: true }
  )
}

const confirmDelete = (productId, productTitle) => {
  if (confirm(locale.value === 'en' 
    ? `Are you sure you want to delete "${productTitle || 'this article'}"? This action cannot be undone.`
    : `確定要刪除「${productTitle || '此文章'}」嗎？此操作無法復原。`)) {
    deleteArticle(productId)
  }
}

const deleteArticle = (productId) => {
  deletingId.value = productId
  router.delete(route('admin.products.destroy', productId), {
    preserveScroll: true,
    onSuccess: () => {
      deletingId.value = null
    },
    onError: () => {
      deletingId.value = null
      alert(locale.value === 'en' ? 'Failed to delete article' : '刪除文章失敗')
    },
  })
}

/** ---------- Dev console：像 DD() 一樣把資料全部印出 ---------- */
const dumpAll = (where = 'mounted') => {
  // 全部 props
  console.group(`[${where}] props`)
  console.log('props', props)
  console.log('props.items', props.items)
  console.log('props.items.data', props.items?.data)
  console.table(props.items?.data ?? [])
  ;(props.items?.data ?? []).forEach((row, i) => {
    console.log(`row#${i}`, {
      slug: row.slug,
      product_title: row.product_title,
      application_title: row.application_title,
      case_title: row.case_title,
      product_status: row.product_status,
      application_status: row.application_status,
      case_status: row.case_status,
    })
  })
  console.groupEnd()

  // 方便在 Console 操作
  window.$props = page.props.value
  window.$rows  = props.items?.data ?? []
}

onMounted(() => dumpAll('mounted'))

// 後端重新丟資料（切頁／搜尋）時重印
watch(() => props.items, () => dumpAll('items updated'), { deep: true })
</script>

<template>
  <Head :title="t('admin.articles.title')" />
  <div class="max-w-7xl mx-auto px-6 md:px-12 lg:px-16 xl:px-24 py-8">
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <h1 class="text-2xl font-bold">{{ t('admin.articles.subtitle') }}</h1>
          <div class="flex gap-2">
            <Link :href="route('admin.carousel.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Carousel' : '輪播管理' }}
            </Link>
            <Link :href="route('admin.footer.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Footer Settings' : '頁尾設定' }}
            </Link>
            <Link :href="route('admin.about.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'About Us' : '關於我們' }}
            </Link>
            <Link :href="route('admin.products.create')" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700">
              {{ t('admin.articles.create_bundle') }}
            </Link>
          </div>
    </header>

    <!-- 篩選列 -->
    <div class="rounded-2xl border bg-white p-4 md:p-5 mb-4">
      <div class="grid md:grid-cols-4 gap-3">
        <input
          v-model="q"
          @keyup.enter="doSearch"
          :placeholder="t('admin.articles.search_placeholder')"
          class="w-full border rounded-lg px-3 py-2"
        />
        <select v-model="status" class="w-full border rounded-lg px-3 py-2">
          <option value="">{{ t('admin.articles.all_status') }}</option>
          <option value="draft">{{ t('admin.form.draft') }}</option>
          <option value="published">{{ t('admin.form.published') }}</option>
        </select>
        <button @click="doSearch" class="px-4 py-2 rounded-xl bg-gray-900 text-white">{{ t('admin.articles.search') }}</button>
      </div>
    </div>

    <!-- 資料表 -->
    <div class="overflow-x-auto rounded-2xl border bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.slug') }}</th>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.product') }}</th>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.application') }}</th>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.case') }}</th>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.last_updated') }}</th>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in items.data" :key="row.product_id" class="border-t">
            <td class="px-4 py-3 font-mono text-xs md:text-sm">{{ row.slug }}</td>

            <!-- 產品 -->
            <td class="px-4 py-3">
              <div class="font-medium">{{ row.product_title || t('admin.articles.no_title') }}</div>
              <div
                class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs"
                :class="row.product_status === 'published'
                  ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                  : 'bg-gray-100 text-gray-700'"
              >
                {{ row.product_status || '—' }}
              </div>
            </td>

            <!-- 應用 -->
            <td class="px-4 py-3">
              <div v-if="row.application_id">
                <div class="font-medium">{{ row.application_title || t('admin.articles.none') }}</div>
                <div
                  class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs"
                  :class="row.application_status === 'published'
                    ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                    : 'bg-gray-100 text-gray-700'"
                >
                  {{ row.application_status || '—' }}
                </div>
              </div>
              <div v-else class="text-gray-400">—</div>
            </td>

            <!-- 案例 -->
            <td class="px-4 py-3">
              <div v-if="row.case_id">
                <div class="font-medium">{{ row.case_title || t('admin.articles.none') }}</div>
                <div
                  class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs"
                  :class="row.case_status === 'published'
                    ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                    : 'bg-gray-100 text-gray-700'"
                >
                  {{ row.case_status || '—' }}
                </div>
              </div>
              <div v-else class="text-gray-400">—</div>
            </td>

            <td class="px-4 py-3 text-gray-600">
              {{ row.last_updated ? new Date(row.last_updated).toLocaleString() : '—' }}
            </td>

            <td class="px-4 py-3">
              <div class="flex gap-2">
                <!-- ✅ 編輯按鈕 -->
                <Link
                  :href="route('admin.products.edit', row.product_id)"
                  class="px-3 py-1.5 rounded-lg border hover:bg-gray-50"
                >
                  {{ t('admin.articles.edit') }}
                </Link>
                <!-- ✅ 刪除按鈕 -->
                <button
                  @click="confirmDelete(row.product_id, row.product_title)"
                  :disabled="deletingId === row.product_id"
                  class="px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ deletingId === row.product_id 
                    ? (locale === 'en' ? 'Deleting...' : '刪除中...') 
                    : (locale === 'en' ? 'Delete' : '刪除') }}
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="!items.data?.length">
            <td colspan="6" class="px-4 py-10 text-center text-gray-500">{{ t('admin.articles.no_data') }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 分頁 -->
    <div class="mt-4 flex flex-wrap items-center gap-2">
      <Link
        v-for="link in items.links"
        :key="(link.url || '') + link.label"
        :href="link.url || '#'"
        v-html="link.label"
        class="px-3 py-1.5 rounded border"
        :class="[
          link.active ? 'bg-gray-900 text-white border-gray-900' : 'hover:bg-gray-50',
          !link.url && 'pointer-events-none opacity-50'
        ]"
      />
    </div>
  </div>
</template>
