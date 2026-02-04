<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import AppLayout from '@/Layouts/AppLayout.vue'

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
    ? `Are you sure you want to delete "${productTitle || 'this product'}"? This action cannot be undone.`
    : `確定要刪除「${productTitle || '此產品'}」嗎？此操作無法復原。`)) {
    deleteProduct(productId)
  }
}

const deleteProduct = (productId) => {
  deletingId.value = productId
  router.delete(route('admin.products.destroy', productId), {
    preserveScroll: true,
    onSuccess: () => {
      deletingId.value = null
    },
    onError: () => {
      deletingId.value = null
      alert(locale.value === 'en' ? 'Failed to delete product' : '刪除產品失敗')
    },
  })
}

</script>

<script>
export default { layout: AppLayout }
</script>

<template>
  <Head :title="t('admin.articles.title')" />
  <div class="max-w-7xl mx-auto">
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <h1 class="text-2xl font-bold">{{ t('admin.articles.subtitle') }}</h1>
          <div class="flex gap-2 flex-wrap">
            <Link :href="route('admin.carousel.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Carousel' : '輪播管理' }}
            </Link>
            <Link :href="route('admin.footer.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Footer Settings' : '頁尾設定' }}
            </Link>
            <Link :href="route('admin.about.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'About Us' : '關於我們' }}
            </Link>
            <Link :href="route('admin.tech.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Core Technology' : '核心技術' }}
            </Link>
            <Link :href="route('admin.applications.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Applications' : '應用場域' }}
            </Link>
            <Link :href="route('admin.cases.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Cases' : '案例說明' }}
            </Link>
            <Link :href="route('admin.contact.index')" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
              {{ locale === 'en' ? 'Contact Settings' : '聯絡我們設定' }}
            </Link>
            <Link :href="route('admin.products.create')" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700">
              {{ t('admin.articles.create_product') || (locale === 'en' ? 'Create Product & Service' : '新增產品與服務') }}
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
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.status') || (locale === 'en' ? 'Status' : '狀態') }}</th>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.last_updated') }}</th>
            <th class="text-left px-4 py-3 font-medium">{{ t('admin.articles.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in items.data" :key="row.product_id" class="border-t">
            <td class="px-4 py-3 font-mono text-xs md:text-sm">{{ row.slug }}</td>

            <!-- 產品名稱 -->
            <td class="px-4 py-3">
              <div class="font-medium">{{ row.product_title || t('admin.articles.no_title') }}</div>
            </td>

            <!-- 狀態 -->
            <td class="px-4 py-3">
              <div
                class="inline-flex items-center px-2 py-0.5 rounded text-xs"
                :class="row.product_status === 'published'
                  ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                  : 'bg-gray-100 text-gray-700'"
              >
                {{ row.product_status === 'published' 
                  ? (locale === 'en' ? 'Published' : '已發布')
                  : (locale === 'en' ? 'Draft' : '草稿') }}
              </div>
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
            <td colspan="5" class="px-4 py-10 text-center text-gray-500">{{ t('admin.articles.no_data') }}</td>
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
