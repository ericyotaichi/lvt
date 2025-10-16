<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  mode: { type: String, default: 'create' },          // 'create' | 'edit'
  product: { type: Object, default: null },
  application: { type: Object, default: null },
  case: { type: Object, default: null },
})

const { t } = useTranslations()

const isDev = import.meta.env.DEV

const form = useForm({
  // product
  product: {
    slug: props.product?.slug ?? '',
    title: props.product?.title ?? '',
    title_en: props.product?.title_en ?? '',
    summary: props.product?.summary ?? '',
    summary_en: props.product?.summary_en ?? '',
    description: props.product?.description ?? '',
    description_en: props.product?.description_en ?? '',
    status: props.product?.status ?? 'draft',
    sort: props.product?.sort ?? 0,
    cover: null, // file
  },
  // application
  application: {
    slug: props.application?.slug ?? '',
    title: props.application?.title ?? '',
    title_en: props.application?.title_en ?? '',
    excerpt: props.application?.excerpt ?? '',
    excerpt_en: props.application?.excerpt_en ?? '',
    content: props.application?.content ?? '',
    content_en: props.application?.content_en ?? '',
    status: props.application?.status ?? (props.product?.status ?? 'draft'),
    sort: props.application?.sort ?? (props.product?.sort ?? 0),
    cover: null,
  },
  // case
  case: {
    slug: props.case?.slug ?? '',
    title: props.case?.title ?? '',
    title_en: props.case?.title_en ?? '',
    excerpt: props.case?.excerpt ?? '',
    excerpt_en: props.case?.excerpt_en ?? '',
    content: props.case?.content ?? '',
    content_en: props.case?.content_en ?? '',
    customer: props.case?.customer ?? '',
    customer_en: props.case?.customer_en ?? '',
    status: props.case?.status ?? (props.product?.status ?? 'draft'),
    sort: props.case?.sort ?? (props.product?.sort ?? 0),
    cover: null,
  },
})

// 檔案資訊快速檢查
const logFiles = () => {
  if (!isDev) return
  const list = [
    ['product.cover', form.product.cover],
    ['application.cover', form.application.cover],
    ['case.cover', form.case.cover],
  ]
  list.forEach(([label, f]) => {
    if (f) {
      const kb = (f.size / 1024).toFixed(1)
      const mb = (f.size / 1024 / 1024).toFixed(2)
      console.log(`[FILE] ${label}: ${f.name} | ${kb} KB (${mb} MB) | type=${f.type}`)
    }
  })
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
  <Head :title="props.mode === 'create' ? t('admin.form.create_bundle') : t('admin.form.edit_bundle')" />

  <h1 class="text-2xl font-bold mb-6">
    {{ props.mode === 'create' ? t('admin.form.create_bundle') : t('admin.form.edit_bundle') }}
  </h1>

  <form @submit.prevent="submit" class="space-y-10">
    <!-- 產品 -->
    <section class="rounded-2xl border bg-white p-6">
      <h2 class="text-xl font-semibold">{{ t('admin.form.product') }}</h2>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.product_name') }} (中文) *</label>
          <input v-model="form.product.title" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" required />
          <p v-if="form.errors['product.title']" class="text-sm text-red-600">{{ form.errors['product.title'] }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.product_name') }} (English)</label>
          <input v-model="form.product.title_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
          <p v-if="form.errors['product.title_en']" class="text-sm text-red-600">{{ form.errors['product.title_en'] }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.slug') }}</label>
          <input v-model="form.product.slug" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" :placeholder="t('admin.form.slug_placeholder')" />
          <p v-if="form.errors['product.slug']" class="text-sm text-red-600">{{ form.errors['product.slug'] }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.status') }}</label>
          <select v-model="form.product.status" class="mt-1 w-full rounded-xl border px-3 py-2">
            <option value="draft">{{ t('admin.form.draft') }}</option>
            <option value="published">{{ t('admin.form.published') }}</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.summary') }} (中文)</label>
          <input v-model="form.product.summary" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.summary') }} (English)</label>
          <input v-model="form.product.summary_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.content') }} (中文)</label>
          <textarea v-model="form.product.description" class="mt-1 w-full rounded-xl border px-3 py-2" rows="4"></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.content') }} (English)</label>
          <textarea v-model="form.product.description_en" class="mt-1 w-full rounded-xl border px-3 py-2" rows="4"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.sort') }}</label>
          <input v-model.number="form.product.sort" type="number" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.cover') }}</label>
          <input type="file" accept="image/*" @change="e => form.product.cover = e.target.files?.[0] ?? null" />
          <p v-if="form.errors['product.cover']" class="text-sm text-red-600">{{ form.errors['product.cover'] }}</p>
        </div>
      </div>
    </section>

    <!-- 應用 -->
    <section class="rounded-2xl border bg-white p-6">
      <h2 class="text-xl font-semibold">{{ t('admin.form.application') }}</h2>
      <p class="text-sm text-gray-500">{{ t('admin.form.application_note') }}</p>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.title') }} (中文)</label>
          <input v-model="form.application.title" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
          <p v-if="form.errors['application.title']" class="text-sm text-red-600">{{ form.errors['application.title'] }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.title') }} (English)</label>
          <input v-model="form.application.title_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
          <p v-if="form.errors['application.title_en']" class="text-sm text-red-600">{{ form.errors['application.title_en'] }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.slug') }}</label>
          <input v-model="form.application.slug" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" :placeholder="t('admin.form.slug_placeholder')" />
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.status') }}</label>
          <select v-model="form.application.status" class="mt-1 w-full rounded-xl border px-3 py-2">
            <option value="draft">{{ t('admin.form.draft') }}</option>
            <option value="published">{{ t('admin.form.published') }}</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.summary') }} (中文)</label>
          <input v-model="form.application.excerpt" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.summary') }} (English)</label>
          <input v-model="form.application.excerpt_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.content') }} (中文)</label>
          <textarea v-model="form.application.content" class="mt-1 w-full rounded-xl border px-3 py-2" rows="4"></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.content') }} (English)</label>
          <textarea v-model="form.application.content_en" class="mt-1 w-full rounded-xl border px-3 py-2" rows="4"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.sort') }}</label>
          <input v-model.number="form.application.sort" type="number" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.cover') }}</label>
          <input type="file" accept="image/*" @change="e => form.application.cover = e.target.files?.[0] ?? null" />
          <p v-if="form.errors['application.cover']" class="text-sm text-red-600">{{ form.errors['application.cover'] }}</p>
        </div>
      </div>
    </section>

    <!-- 案例 -->
    <section class="rounded-2xl border bg-white p-6">
      <h2 class="text-xl font-semibold">{{ t('admin.form.case') }}</h2>
      <p class="text-sm text-gray-500">{{ t('admin.form.case_note') }}</p>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.title') }} (中文)</label>
          <input v-model="form.case.title" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.title') }} (English)</label>
          <input v-model="form.case.title_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.slug') }}</label>
          <input v-model="form.case.slug" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" :placeholder="t('admin.form.slug_placeholder')" />
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.status') }}</label>
          <select v-model="form.case.status" class="mt-1 w-full rounded-xl border px-3 py-2">
            <option value="draft">{{ t('admin.form.draft') }}</option>
            <option value="published">{{ t('admin.form.published') }}</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.summary') }} (中文)</label>
          <input v-model="form.case.excerpt" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.summary') }} (English)</label>
          <input v-model="form.case.excerpt_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.content') }} (中文)</label>
          <textarea v-model="form.case.content" class="mt-1 w-full rounded-xl border px-3 py-2" rows="4"></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.content') }} (English)</label>
          <textarea v-model="form.case.content_en" class="mt-1 w-full rounded-xl border px-3 py-2" rows="4"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.customer_name') }} (中文)</label>
          <input v-model="form.case.customer" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.customer_name') }} (English)</label>
          <input v-model="form.case.customer_en" type="text" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium">{{ t('admin.form.sort') }}</label>
          <input v-model.number="form.case.sort" type="number" class="mt-1 w-full rounded-xl border px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">{{ t('admin.form.cover') }}</label>
          <input type="file" accept="image/*" @change="e => form.case.cover = e.target.files?.[0] ?? null" />
          <p v-if="form.errors['case.cover']" class="text-sm text-red-600">{{ form.errors['case.cover'] }}</p>
        </div>
      </div>
    </section>

    <div class="flex gap-2">
      <button
        type="button"
        :disabled="form.processing"
        @click="submit"
        class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 disabled:opacity-60"
      >
        {{ form.processing ? t('admin.form.saving') : t('admin.form.save') }}
      </button>
      <button type="button" @click="backToIndex" class="px-4 py-2 rounded-xl border hover:bg-gray-50">
        {{ t('admin.form.back_to_list') }}
      </button>
      <!-- 臨時 debug 按鈕（用完可刪） -->
      <!-- <button type="button" class="px-3 py-1.5 border rounded"
              @click="() => { console.log('[DEBUG] now=', form.data()); logFiles(); }">
        console 檢查
      </button> -->
    </div>
  </form>
</template>
