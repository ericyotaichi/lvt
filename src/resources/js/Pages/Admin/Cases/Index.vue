<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  filters: Object,
  items: Object,
})

const page = usePage()
const { t } = useTranslations()
const locale = computed(() => page.props.locale || 'zh')

const q = ref(props.filters?.q ?? '')
const status = ref(props.filters?.status ?? '')
const deletingId = ref(null)

const doSearch = () => {
  router.get(
    route('admin.cases.index'),
    { q: q.value || undefined, status: status.value || undefined },
    { preserveState: true, replace: true }
  )
}

const confirmDelete = (id, title) => {
  const message = locale.value === 'en'
    ? `Are you sure you want to delete "${title || 'this case'}"? This action cannot be undone.`
    : `確定要刪除「${title || '此案例'}」嗎？此操作無法復原。`
  if (confirm(message)) {
    deletingId.value = id
    router.delete(route('admin.cases.destroy', id), {
      preserveScroll: true,
      onFinish: () => {
        deletingId.value = null
      },
    })
  }
}
</script>

<script>
export default { layout: AppLayout }
</script>

<template>
  <Head title="案例說明管理" />

  <div class="max-w-7xl mx-auto">
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold">案例說明管理</h1>
        <p class="text-gray-600 mt-1">建立與維護案例文章，簡單獨立的內容頁。</p>
      </div>
      <Link
        :href="route('admin.cases.create')"
        class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700"
      >
        新增案例說明
      </Link>
    </header>

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
        <button @click="doSearch" class="px-4 py-2 rounded-xl bg-gray-900 text-white">
          {{ t('admin.articles.search') }}
        </button>
      </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-4 py-3 font-medium">Slug</th>
            <th class="text-left px-4 py-3 font-medium">案例名稱</th>
            <th class="text-left px-4 py-3 font-medium">客戶名稱</th>
            <th class="text-left px-4 py-3 font-medium">狀態</th>
            <th class="text-left px-4 py-3 font-medium">更新時間</th>
            <th class="text-left px-4 py-3 font-medium">操作</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in items.data" :key="row.id" class="border-t">
            <td class="px-4 py-3 font-mono text-xs md:text-sm">{{ row.slug }}</td>
            <td class="px-4 py-3">
              <div class="font-medium">{{ row.title }}</div>
            </td>
            <td class="px-4 py-3">
              <div class="text-sm text-gray-600">{{ row.customer || '—' }}</div>
            </td>
            <td class="px-4 py-3">
              <div
                class="inline-flex items-center px-2 py-0.5 rounded text-xs"
                :class="row.status === 'published'
                  ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                  : 'bg-gray-100 text-gray-700'"
              >
                {{ row.status === 'published'
                  ? (locale === 'en' ? 'Published' : '已發布')
                  : (locale === 'en' ? 'Draft' : '草稿') }}
              </div>
            </td>
            <td class="px-4 py-3 text-gray-600">
              {{ row.updated_at ? new Date(row.updated_at).toLocaleString() : '—' }}
            </td>
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <Link
                  :href="route('admin.cases.edit', row.id)"
                  class="px-3 py-1.5 rounded-lg border hover:bg-gray-50"
                >
                  {{ t('admin.articles.edit') }}
                </Link>
                <button
                  @click="confirmDelete(row.id, row.title)"
                  :disabled="deletingId === row.id"
                  class="px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ deletingId === row.id
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
