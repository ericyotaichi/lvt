<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  footer: { type: Object, default: null }
})

const { t } = useTranslations()
const page = usePage()

const form = useForm({
  content_zh: props.footer?.content_zh || '© {{year}} YourCompany. All rights reserved.',
  content_en: props.footer?.content_en || '© {{year}} YourCompany. All rights reserved.',
})

const submit = () => {
  form.put(route('admin.footer.update'))
}

// 预览内容（替换 {{year}} 占位符）
const previewZh = computed(() => {
  return form.content_zh.replace(/\{\{year\}\}/g, new Date().getFullYear())
})

const previewEn = computed(() => {
  return form.content_en.replace(/\{\{year\}\}/g, new Date().getFullYear())
})
</script>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head title="頁尾內容管理" />

  <!-- 成功訊息 -->
  <div v-if="page.props.flash?.success" class="mb-4 rounded-xl border bg-green-50 text-green-800 px-4 py-3">
    {{ page.props.flash.success }}
  </div>

  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold mb-6">頁尾內容管理</h1>
    <p class="text-gray-600 mb-8">編輯網站頁尾顯示的內容。可使用 <code class="bg-gray-100 px-1 rounded" v-text="'{{year}}'"></code> 來顯示當前年份。</p>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- 中文內容 -->
      <div class="rounded-2xl border bg-white p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          中文頁尾內容 <span class="text-red-500">*</span>
        </label>
        <textarea
          v-model="form.content_zh"
          rows="3"
          class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
          :placeholder="`© ${new Date().getFullYear()} YourCompany. All rights reserved.`"
          required
        ></textarea>
        <p v-if="form.errors.content_zh" class="mt-1 text-sm text-red-600">{{ form.errors.content_zh }}</p>
        <p class="mt-2 text-xs text-gray-500">預覽：{{ previewZh }}</p>
      </div>

      <!-- 英文內容 -->
      <div class="rounded-2xl border bg-white p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          英文頁尾內容 <span class="text-red-500">*</span>
        </label>
        <textarea
          v-model="form.content_en"
          rows="3"
          class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
          :placeholder="`© ${new Date().getFullYear()} YourCompany. All rights reserved.`"
          required
        ></textarea>
        <p v-if="form.errors.content_en" class="mt-1 text-sm text-red-600">{{ form.errors.content_en }}</p>
        <p class="mt-2 text-xs text-gray-500">Preview: {{ previewEn }}</p>
      </div>

      <!-- 提交按鈕 -->
      <div class="flex items-center justify-end gap-4">
        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center justify-center rounded-xl bg-green-600 text-white px-6 py-2.5 font-medium hover:bg-green-700 disabled:opacity-60"
        >
          {{ form.processing ? '儲存中…' : '儲存' }}
        </button>
      </div>
    </form>
  </div>
</template>

