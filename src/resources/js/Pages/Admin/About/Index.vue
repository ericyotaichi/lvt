<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  about: { type: Object, default: null }
})

const { t } = useTranslations()
const page = usePage()

const form = useForm({
  content_zh: props.about?.content_zh || '',
  content_en: props.about?.content_en || '',
  image_url: props.about?.image_url || '',
  image: null, // 文件上传
})

const imagePreview = ref(props.about?.image_url || null)
const imageInputRef = ref(null)

const handleImageChange = (event) => {
  const file = event.target.files?.[0] ?? null
  if (file) {
    form.image = file
    // 创建预览
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  } else {
    form.image = null
    if (!form.image_url) {
      imagePreview.value = null
    }
  }
}

const removeImage = () => {
  form.image = null
  form.image_url = ''
  imagePreview.value = null
  if (imageInputRef.value) {
    imageInputRef.value.value = ''
  }
}

const submit = () => {
  // 确保文件上传时使用正确的 Content-Type
  const options = {
    forceFormData: true, // 强制使用 FormData 以支持文件上传
    preserveScroll: true,
    onSuccess: () => {
      // 成功后刷新页面数据
      window.location.reload()
    },
    onError: (errors) => {
      console.error('Form errors:', errors)
    },
  }
  
  // 对于更新，使用 transform 添加 _method，然后使用 post
  form.transform((data) => {
    // 确保文件对象被保留
    const transformed = { ...data, _method: 'PUT' }
    // 如果 form.image 存在，确保它被包含
    if (form.image) {
      transformed.image = form.image
    }
    // 如果没有上传新图片且没有 image_url，保留原有的 image_url
    if (!transformed.image && !transformed.image_url && props.about?.image_url) {
      transformed.image_url = props.about.image_url
    }
    return transformed
  }).post(route('admin.about.update'), options)
}
</script>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head title="關於我們內容管理" />

  <!-- 成功訊息 -->
  <div v-if="page.props.flash?.success" class="mb-4 rounded-xl border bg-green-50 text-green-800 px-4 py-3">
    {{ page.props.flash.success }}
  </div>

  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold mb-6">關於我們內容管理</h1>
    <p class="text-gray-600 mb-8">編輯「關於我們」頁面的公司介紹內容和圖片。</p>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- 中文內容 -->
      <div class="rounded-2xl border bg-white p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          中文公司介紹內容 <span class="text-red-500">*</span>
        </label>
        <textarea
          v-model="form.content_zh"
          rows="10"
          class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
          placeholder="請輸入中文公司介紹內容..."
          required
        ></textarea>
        <p v-if="form.errors.content_zh" class="mt-1 text-sm text-red-600">{{ form.errors.content_zh }}</p>
      </div>

      <!-- 英文內容 -->
      <div class="rounded-2xl border bg-white p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          英文公司介紹內容 <span class="text-red-500">*</span>
        </label>
        <textarea
          v-model="form.content_en"
          rows="10"
          class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
          placeholder="Please enter English company introduction content..."
          required
        ></textarea>
        <p v-if="form.errors.content_en" class="mt-1 text-sm text-red-600">{{ form.errors.content_en }}</p>
      </div>

      <!-- 圖片上傳 -->
      <div class="rounded-2xl border bg-white p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          公司介紹圖片
        </label>
        
        <!-- 圖片預覽 -->
        <div v-if="imagePreview" class="mt-2 mb-4">
          <div class="relative inline-block">
            <img 
              :src="imagePreview" 
              alt="預覽圖片" 
              class="max-w-md rounded-xl border"
            />
            <button
              type="button"
              @click="removeImage"
              class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600"
            >
              ×
            </button>
          </div>
        </div>

        <!-- 上傳按鈕 -->
        <div class="mt-2">
          <input
            ref="imageInputRef"
            type="file"
            accept="image/*"
            @change="handleImageChange"
            class="hidden"
            id="image-upload"
          />
          <label
            for="image-upload"
            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer"
          >
            選擇圖片
          </label>
          <span v-if="form.image" class="ml-3 text-sm text-gray-600">
            {{ form.image.name }}
          </span>
        </div>

        <!-- 或輸入圖片 URL -->
        <div class="mt-4">
          <label class="block text-xs text-gray-500 mb-1">或輸入圖片 URL</label>
          <input
            v-model="form.image_url"
            type="text"
            placeholder="https://example.com/image.jpg"
            class="mt-1 w-full rounded-xl border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600/30"
            @input="imagePreview = form.image_url || null"
          />
        </div>

        <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
        <p v-if="form.errors.image_url" class="mt-1 text-sm text-red-600">{{ form.errors.image_url }}</p>
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

