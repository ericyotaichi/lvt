<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, watch, ref } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const props = defineProps({
  mode: { type: String, default: 'create' },          // 'create' | 'edit'
  product: { type: Object, default: null },
})

const { t } = useTranslations()

const isDev = import.meta.env.DEV

// 封面图片预览
const coverPreview = ref(null)
const coverInputRef = ref(null)

const form = useForm({
  // product only
  slug: props.product?.slug ?? '',
  title: props.product?.title ?? '',
  title_en: props.product?.title_en ?? '',
  summary: props.product?.summary ?? '',
  summary_en: props.product?.summary_en ?? '',
  content: props.product?.content ?? '',
  content_en: props.product?.content_en ?? '',
  status: props.product?.status ?? 'draft',
  sort: props.product?.sort ?? 0,
  cover: null, // file
})

// 處理封面圖片選擇
const handleCoverChange = (event) => {
  const file = event.target.files?.[0] ?? null
  form.cover = file
  
  // 生成預覽
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      coverPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  } else {
    coverPreview.value = null
  }
  
  // 檔案資訊快速檢查
  if (isDev && file) {
    const kb = (file.size / 1024).toFixed(1)
    const mb = (file.size / 1024 / 1024).toFixed(2)
    console.log(`[FILE] cover: ${file.name} | ${kb} KB (${mb} MB) | type=${file.type}`)
  }
}

// 檔案資訊快速檢查（保留用於提交時）
const logFiles = () => {
  if (!isDev) return
  if (form.cover) {
    const kb = (form.cover.size / 1024).toFixed(1)
    const mb = (form.cover.size / 1024 / 1024).toFixed(2)
    console.log(`[FILE] cover: ${form.cover.name} | ${kb} KB (${mb} MB) | type=${form.cover.type}`)
  }
}

// 表單變更監聽（僅 dev 印出）
watch(
  () => form.data(),
  (v) => { if (isDev) console.log('[FORM] changed:', v) },
  { deep: true }
)

// 返回首頁
const initial = JSON.stringify(form.data())
const dirty = computed(() => JSON.stringify(form.data()) !== initial)
const backToIndex = () => {
  if (dirty.value && !confirm(t('admin.form.unsaved_changes'))) return
  router.visit(route('admin.articles.index'))
}

const submit = () => {
  if (isDev) {
    console.log('[SUBMIT] mode=', props.mode)
    console.log('[SUBMIT] payload snapshot →', JSON.parse(JSON.stringify(form.data())))
    logFiles()
  }

  if (props.mode === 'create') {
    // 新增：直接 POST，強制用 FormData（含檔案）
    form.post(route('admin.products.store'), {
      forceFormData: true,
      onError: (errs) => { if (isDev) console.warn('[SUBMIT][ERROR]', errs) },
      onSuccess: () => { if (isDev) console.log('[SUBMIT] create → success') },
      onFinish: () => { if (isDev) console.log('[SUBMIT] create → finished') },
    })
  } else {
    // 編輯：改成 POST 並帶 _method=PUT，強制 FormData
    form.transform(data => ({ ...data, _method: 'put' }))
    form.post(route('admin.products.update', props.product.id), {
      forceFormData: true,
      onError: (errs) => { if (isDev) console.warn('[SUBMIT][ERROR]', errs) },
      onSuccess: () => { if (isDev) console.log('[SUBMIT] update → success') },
      onFinish: () => { if (isDev) console.log('[SUBMIT] update → finished') },
    })
  }
}
</script>

<script>
// 套用你的 AppLayout
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head :title="props.mode === 'create' ? '新增產品與服務' : '編輯產品與服務'" />

  <h1 class="text-2xl font-bold mb-6">
    {{ props.mode === 'create' ? '新增產品與服務' : '編輯產品與服務' }}
  </h1>

  <form @submit.prevent="submit" class="space-y-6">
    <!-- 產品與服務 -->
    <section class="rounded-2xl border bg-white p-6">
      <h2 class="text-xl font-semibold mb-4">產品與服務內容</h2>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">產品名稱 (中文) *</label>
          <input v-model="form.title" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" required />
          <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">產品名稱 (English)</label>
          <input v-model="form.title_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
          <p v-if="form.errors.title_en" class="text-sm text-red-600">{{ form.errors.title_en }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">Slug</label>
          <input v-model="form.slug" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="留空自動產生" />
          <p v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</p>
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
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">摘要 (中文)</label>
          <input v-model="form.summary" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">摘要 (English)</label>
          <input v-model="form.summary_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-2">內容 (中文) - 富文本編輯</label>
          <RichTextEditor v-model="form.content" placeholder="請輸入內容..." />
          <p v-if="form.errors.content" class="text-sm text-red-600 mt-1">{{ form.errors.content }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-2">內容 (English) - 富文本編輯</label>
          <RichTextEditor v-model="form.content_en" placeholder="Please enter content..." />
          <p v-if="form.errors.content_en" class="text-sm text-red-600 mt-1">{{ form.errors.content_en }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">封面圖片</label>
          <input 
            ref="coverInputRef"
            type="file" 
            accept="image/*" 
            @change="handleCoverChange" 
            class="mt-1 w-full rounded-xl border px-3 py-2"
          />
          <p v-if="form.errors.cover" class="text-sm text-red-600">{{ form.errors.cover }}</p>
          
          <!-- 顯示預覽：優先顯示新選擇的圖片，否則顯示現有封面 -->
          <div v-if="coverPreview || (props.product?.cover_url && !form.cover)" class="mt-2">
            <p class="text-sm text-gray-500 mb-1">{{ coverPreview ? '預覽' : '目前封面' }}：</p>
            <div class="relative inline-block">
              <img 
                :src="coverPreview || props.product.cover_url" 
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
</template>
