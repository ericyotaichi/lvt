<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const props = defineProps({
  mode: { type: String, default: 'create' },
  caseItem: { type: Object, default: null },
})

const { t } = useTranslations()
const isDev = import.meta.env.DEV

const coverPreview = ref(null)
const coverInputRef = ref(null)

const form = useForm({
  title: props.caseItem?.title ?? '',
  title_en: props.caseItem?.title_en ?? '',
  excerpt: props.caseItem?.excerpt ?? '',
  excerpt_en: props.caseItem?.excerpt_en ?? '',
  content: props.caseItem?.content ?? '',
  content_en: props.caseItem?.content_en ?? '',
  customer: props.caseItem?.customer ?? '',
  customer_en: props.caseItem?.customer_en ?? '',
  status: props.caseItem?.status ?? 'draft',
  sort: props.caseItem?.sort ?? 0,
  cover: null,
})

const handleCoverChange = (event) => {
  const file = event.target.files?.[0] ?? null
  form.cover = file

  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      coverPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  } else {
    coverPreview.value = null
  }

  if (isDev && file) {
    const kb = (file.size / 1024).toFixed(1)
    const mb = (file.size / 1024 / 1024).toFixed(2)
    console.log(`[FILE] cover: ${file.name} | ${kb} KB (${mb} MB) | type=${file.type}`)
  }
}

const logFiles = () => {
  if (!isDev) return
  if (form.cover) {
    const kb = (form.cover.size / 1024).toFixed(1)
    const mb = (form.cover.size / 1024 / 1024).toFixed(2)
    console.log(`[FILE] cover: ${form.cover.name} | ${kb} KB (${mb} MB) | type=${form.cover.type}`)
  }
}

watch(
  () => form.data(),
  (v) => { if (isDev) console.log('[FORM] changed:', v) },
  { deep: true }
)

const initial = JSON.stringify(form.data())
const dirty = computed(() => JSON.stringify(form.data()) !== initial)
const backToIndex = () => {
  if (dirty.value && !confirm(t('admin.form.unsaved_changes'))) return
  router.visit(route('admin.cases.index'))
}

const submit = () => {
  if (isDev) {
    console.log('[SUBMIT] mode=', props.mode)
    console.log('[SUBMIT] payload snapshot →', JSON.parse(JSON.stringify(form.data())))
    logFiles()
  }

  if (props.mode === 'create') {
    form.post(route('admin.cases.store'), {
      forceFormData: true,
      onError: (errs) => { if (isDev) console.warn('[SUBMIT][ERROR]', errs) },
    })
  } else {
    form.transform(data => ({ ...data, _method: 'put' }))
    form.post(route('admin.cases.update', props.caseItem.id), {
      forceFormData: true,
      onError: (errs) => { if (isDev) console.warn('[SUBMIT][ERROR]', errs) },
    })
  }
}
</script>

<script>
export default { layout: AppLayout }
</script>

<template>
  <Head :title="props.mode === 'create' ? '新增案例說明' : '編輯案例說明'" />

  <div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold">
          {{ props.mode === 'create' ? '新增案例說明' : '編輯案例說明' }}
        </h1>
        <p class="text-gray-600 mt-1">此分頁獨立呈現，不與其他模組連動。</p>
      </div>
      <button type="button" @click="backToIndex" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
        返回列表
      </button>
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <section class="rounded-2xl border bg-white p-6">
        <h2 class="text-xl font-semibold mb-4">基本資訊</h2>
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium">案例名稱 (中文) *</label>
            <input v-model="form.title" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" required />
            <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium">案例名稱 (English)</label>
            <input v-model="form.title_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
            <p v-if="form.errors.title_en" class="text-sm text-red-600">{{ form.errors.title_en }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium">狀態</label>
            <select v-model="form.status" class="mt-1 w-full rounded-xl border px-3 py-2">
              <option value="draft">草稿</option>
              <option value="published">已發布</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">排序</label>
            <input v-model.number="form.sort" type="number" class="mt-1 w-full rounded-xl border px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">客戶名稱 (中文)</label>
            <input v-model="form.customer" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
            <p v-if="form.errors.customer" class="text-sm text-red-600">{{ form.errors.customer }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium">客戶名稱 (English)</label>
            <input v-model="form.customer_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
            <p v-if="form.errors.customer_en" class="text-sm text-red-600">{{ form.errors.customer_en }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium">摘要 (中文)</label>
            <input v-model="form.excerpt" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium">摘要 (English)</label>
            <input v-model="form.excerpt_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
          </div>
        </div>
      </section>

      <section class="rounded-2xl border bg-white p-6 space-y-4">
        <h2 class="text-xl font-semibold">內容</h2>
        <div>
          <label class="block text-sm font-medium mb-2">內容 (中文)</label>
          <RichTextEditor v-model="form.content" placeholder="請輸入案例內容..." />
          <p v-if="form.errors.content" class="text-sm text-red-600 mt-1">{{ form.errors.content }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">內容 (English)</label>
          <RichTextEditor v-model="form.content_en" placeholder="Please enter case content..." />
          <p v-if="form.errors.content_en" class="text-sm text-red-600 mt-1">{{ form.errors.content_en }}</p>
        </div>
      </section>

      <section class="rounded-2xl border bg-white p-6">
        <h2 class="text-xl font-semibold mb-4">封面圖片</h2>
        <input
          ref="coverInputRef"
          type="file"
          accept="image/*"
          @change="handleCoverChange"
          class="mt-1 w-full rounded-xl border px-3 py-2"
        />
        <p v-if="form.errors.cover" class="text-sm text-red-600">{{ form.errors.cover }}</p>

        <div v-if="coverPreview || (props.caseItem?.cover_url && !form.cover)" class="mt-3">
          <p class="text-sm text-gray-500 mb-2">{{ coverPreview ? '預覽' : '目前封面' }}：</p>
          <div class="relative inline-block">
            <img
              :src="coverPreview || props.caseItem?.cover_url"
              alt="Cover preview"
              class="w-48 h-48 object-cover rounded-lg border"
            />
            <button
              v-if="coverPreview"
              type="button"
              @click="coverPreview = null; form.cover = null; coverInputRef.value = ''"
              class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 text-sm"
            >
              ×
            </button>
          </div>
        </div>
      </section>

      <div class="flex gap-2">
        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 disabled:opacity-60"
        >
          {{ form.processing ? '儲存中…' : '儲存' }}
        </button>
        <button type="button" @click="backToIndex" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
          返回列表
        </button>
      </div>
    </form>
  </div>
</template>
