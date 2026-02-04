<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const props = defineProps({
  tech: { type: Object, default: null }
})

const page = usePage()
const locale = computed(() => page.props.locale || 'zh')

const form = useForm({
  title_zh: props.tech?.title_zh || '',
  title_en: props.tech?.title_en || '',
  content_zh: props.tech?.content_zh || '',
  content_en: props.tech?.content_en || '',
})

const submit = () => {
  form.transform((data) => ({
    ...data,
    _method: 'PUT'
  })).post(route('admin.tech.update'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      // 成功后刷新页面数据
      window.location.reload()
    },
  })
}
</script>

<script>
export default { layout: AppLayout }
</script>

<template>
  <Head title="核心技術內容管理" />

  <!-- 成功訊息 -->
  <div v-if="page.props.flash?.success" class="mb-4 rounded-xl border bg-green-50 text-green-800 px-4 py-3">
    {{ page.props.flash.success }}
  </div>

  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold mb-6">核心技術內容管理</h1>
    <p class="text-gray-600 mb-8">編輯「核心技術」頁面的標題和內容。</p>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- 中文版 -->
      <div class="rounded-2xl border bg-white p-6">
        <h2 class="text-xl font-bold mb-4">中文版</h2>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            標題 <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.title_zh"
            type="text"
            class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
            placeholder="請輸入中文標題..."
            required
          />
          <p v-if="form.errors.title_zh" class="mt-1 text-sm text-red-600">{{ form.errors.title_zh }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            內容 <span class="text-red-500">*</span>
          </label>
          <RichTextEditor
            v-model="form.content_zh"
            placeholder="請輸入中文內容..."
          />
          <p v-if="form.errors.content_zh" class="mt-1 text-sm text-red-600">{{ form.errors.content_zh }}</p>
        </div>
      </div>

      <!-- 英文版 -->
      <div class="rounded-2xl border bg-white p-6">
        <h2 class="text-xl font-bold mb-4">英文版</h2>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            標題
          </label>
          <input
            v-model="form.title_en"
            type="text"
            class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
            placeholder="Please enter English title..."
          />
          <p v-if="form.errors.title_en" class="mt-1 text-sm text-red-600">{{ form.errors.title_en }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            內容
          </label>
          <RichTextEditor
            v-model="form.content_en"
            placeholder="Please enter English content..."
          />
          <p v-if="form.errors.content_en" class="mt-1 text-sm text-red-600">{{ form.errors.content_en }}</p>
        </div>
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

