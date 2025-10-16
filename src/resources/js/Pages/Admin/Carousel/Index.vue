<script setup>
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps({
  slides: { type: Array, default: () => [] }
})

const { t } = useTranslations()
const page = usePage()
const locale = computed(() => page.props.locale || 'zh')

const editingId = ref(null)
const showForm = ref(false)

const form = useForm({
  title: '',
  title_en: '',
  description: '',
  description_en: '',
  image_url: '',
  image: null, // 文件上传
  link_url: '',
  link_text: '',
  link_text_en: '',
  sort: 0,
  status: true,
})

const imagePreview = ref(null)
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
    console.log('Image file selected:', { name: file.name, size: file.size, type: file.type })
  } else {
    form.image = null
    imagePreview.value = null
  }
}

const removeImage = () => {
  form.image = null
  imagePreview.value = null
  if (imageInputRef.value) {
    imageInputRef.value.value = ''
  }
}

const startEdit = (slide) => {
  editingId.value = slide.id
  form.title = slide.title || ''
  form.title_en = slide.title_en || ''
  form.description = slide.description || ''
  form.description_en = slide.description_en || ''
  form.image_url = slide.image_url || ''
  form.image = null // 重置文件
  form.link_url = slide.link_url || ''
  form.link_text = slide.link_text || ''
  form.link_text_en = slide.link_text_en || ''
  form.sort = slide.sort || 0
  form.status = slide.status !== undefined ? slide.status : true
  imagePreview.value = slide.image_url || null
  showForm.value = true
}

const startCreate = () => {
  editingId.value = null
  form.reset()
  form.status = true
  imagePreview.value = null
  showForm.value = true
}

const cancelEdit = () => {
  editingId.value = null
  showForm.value = false
  form.reset()
  imagePreview.value = null
  removeImage()
}

const submit = () => {
  // 确保文件上传时使用正确的 Content-Type
  const options = {
    forceFormData: true, // 强制使用 FormData 以支持文件上传
    preserveScroll: true,
    onSuccess: () => {
      cancelEdit()
    },
    onError: (errors) => {
      console.error('Form errors:', errors)
    },
    onBefore: () => {
      // 调试：检查表单数据
      const formData = form.data()
      console.log('Submitting form:', {
        hasImage: !!form.image,
        imageFile: form.image,
        imageUrl: form.image_url,
        title: form.title,
        formData: formData,
        hasImageInFormData: 'image' in formData
      })
    }
  }
  
  if (editingId.value) {
    // 对于更新，使用 transform 添加 _method，然后使用 post
    // 注意：参考 ProductBundleForm 的实现方式
    form.transform((data) => {
      // 确保文件对象被保留
      const transformed = { ...data, _method: 'PUT' }
      // 如果 form.image 存在，确保它被包含
      if (form.image) {
        transformed.image = form.image
      }
      console.log('Transformed data:', transformed, 'Has image:', 'image' in transformed, 'Image type:', typeof transformed.image)
      return transformed
    }).post(route('admin.carousel.update', editingId.value), options)
  } else {
    // 确保文件被包含在提交中
    const formData = form.data()
    console.log('Form data before submit:', formData, 'Has image:', 'image' in formData)
    console.log('Form object image:', form.image, 'Type:', typeof form.image)
    // 直接使用 post，forceFormData 会自动处理文件
    // 确保文件对象在提交时存在
    if (!form.image && !form.image_url) {
      alert(locale.value === 'en' ? 'Please upload an image or enter an image URL' : '請上傳圖片或輸入圖片URL')
      return
    }
    form.post(route('admin.carousel.store'), options)
  }
}

const deleteSlide = (id) => {
  if (confirm(locale.value === 'en' ? 'Are you sure you want to delete this slide?' : '確定要刪除此輪播項目嗎？')) {
    router.delete(route('admin.carousel.destroy', id))
  }
}
</script>

<script>
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head :title="locale === 'en' ? 'Carousel Management' : '輪播管理'" />

  <!-- 成功訊息 -->
  <div v-if="page.props.flash?.success" class="mb-4 rounded-xl border bg-green-50 text-green-800 px-4 py-3">
    {{ page.props.flash.success }}
  </div>

  <div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl md:text-3xl font-bold">{{ locale === 'en' ? 'Carousel Management' : '輪播管理' }}</h1>
      <button
        @click="startCreate"
        class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700"
      >
        {{ locale === 'en' ? 'Add Slide' : '新增輪播項目' }}
      </button>
    </div>

    <!-- 編輯表單 -->
    <div v-if="showForm" class="mb-8 rounded-2xl border bg-white p-6">
      <h2 class="text-xl font-bold mb-4">
        {{ editingId ? (locale === 'en' ? 'Edit Slide' : '編輯輪播項目') : (locale === 'en' ? 'Add Slide' : '新增輪播項目') }}
      </h2>
      
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
          <!-- 中文標題 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Title (Chinese)' : '標題（中文）' }}
            </label>
            <input
              v-model="form.title"
              type="text"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter Chinese title' : '輸入中文標題'"
            />
            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
          </div>

          <!-- 英文標題 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Title (English)' : '標題（英文）' }}
            </label>
            <input
              v-model="form.title_en"
              type="text"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter English title' : '輸入英文標題'"
            />
            <p v-if="form.errors.title_en" class="mt-1 text-sm text-red-600">{{ form.errors.title_en }}</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <!-- 中文描述 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Description (Chinese)' : '描述（中文）' }}
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter Chinese description' : '輸入中文描述'"
            ></textarea>
            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
          </div>

          <!-- 英文描述 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Description (English)' : '描述（英文）' }}
            </label>
            <textarea
              v-model="form.description_en"
              rows="3"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter English description' : '輸入英文描述'"
            ></textarea>
            <p v-if="form.errors.description_en" class="mt-1 text-sm text-red-600">{{ form.errors.description_en }}</p>
          </div>
        </div>

        <!-- 圖片上傳 -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ locale === 'en' ? 'Image' : '圖片' }}
          </label>
          
          <!-- 文件上傳 -->
          <div class="mt-1">
            <input
              ref="imageInputRef"
              type="file"
              accept="image/*"
              @change="handleImageChange"
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
            />
            <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
            <p class="mt-1 text-xs text-gray-500">
              {{ locale === 'en' ? 'Max file size: 5MB. Supported formats: JPG, PNG, GIF, WebP' : '最大檔案大小：5MB。支援格式：JPG、PNG、GIF、WebP' }}
            </p>
          </div>

          <!-- 圖片預覽 -->
          <div v-if="imagePreview || form.image_url" class="mt-4">
            <div class="relative inline-block">
              <img 
                :src="imagePreview || form.image_url" 
                alt="Preview" 
                class="max-w-xs rounded-lg border"
              />
              <button
                v-if="imagePreview || form.image_url"
                @click="removeImage"
                type="button"
                class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600"
              >
                ×
              </button>
            </div>
          </div>

          <!-- 或使用圖片URL -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Or enter image URL' : '或輸入圖片URL' }}
            </label>
            <input
              v-model="form.image_url"
              type="text"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter image URL (optional)' : '輸入圖片URL（選填）'"
            />
            <p v-if="form.errors.image_url" class="mt-1 text-sm text-red-600">{{ form.errors.image_url }}</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <!-- 連結URL -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Link URL (Optional)' : '連結URL（選填）' }}
            </label>
            <input
              v-model="form.link_url"
              type="text"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter link URL' : '輸入連結URL'"
            />
            <p v-if="form.errors.link_url" class="mt-1 text-sm text-red-600">{{ form.errors.link_url }}</p>
          </div>

          <!-- 排序 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Sort Order' : '排序' }}
            </label>
            <input
              v-model="form.sort"
              type="number"
              min="0"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
            />
            <p v-if="form.errors.sort" class="mt-1 text-sm text-red-600">{{ form.errors.sort }}</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <!-- 連結文字（中文） -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Link Text (Chinese)' : '連結文字（中文）' }}
            </label>
            <input
              v-model="form.link_text"
              type="text"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter Chinese link text' : '輸入中文連結文字'"
            />
            <p v-if="form.errors.link_text" class="mt-1 text-sm text-red-600">{{ form.errors.link_text }}</p>
          </div>

          <!-- 連結文字（英文） -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ locale === 'en' ? 'Link Text (English)' : '連結文字（英文）' }}
            </label>
            <input
              v-model="form.link_text_en"
              type="text"
              class="w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30"
              :placeholder="locale === 'en' ? 'Enter English link text' : '輸入英文連結文字'"
            />
            <p v-if="form.errors.link_text_en" class="mt-1 text-sm text-red-600">{{ form.errors.link_text_en }}</p>
          </div>
        </div>

        <!-- 狀態 -->
        <div>
          <label class="flex items-center gap-2">
            <input
              v-model="form.status"
              type="checkbox"
              class="rounded border-gray-300"
            />
            <span class="text-sm font-medium text-gray-700">
              {{ locale === 'en' ? 'Enabled' : '啟用' }}
            </span>
          </label>
        </div>

        <!-- 按鈕 -->
        <div class="flex items-center justify-end gap-4 pt-4">
          <button
            type="button"
            @click="cancelEdit"
            class="px-4 py-2 rounded-xl border hover:bg-gray-50"
          >
            {{ locale === 'en' ? 'Cancel' : '取消' }}
          </button>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 disabled:opacity-60"
          >
            {{ form.processing ? (locale === 'en' ? 'Saving...' : '儲存中…') : (locale === 'en' ? 'Save' : '儲存') }}
          </button>
        </div>
      </form>
    </div>

    <!-- 輪播項目列表 -->
    <div class="space-y-4">
      <div
        v-for="slide in slides"
        :key="slide.id"
        class="rounded-2xl border bg-white p-6"
      >
        <div class="flex items-start gap-4">
          <div v-if="slide.image_url" class="flex-shrink-0">
            <img :src="slide.image_url" :alt="slide.title" class="w-32 h-20 object-cover rounded-lg" />
          </div>
          <div class="flex-1">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-lg font-semibold">{{ slide.title || slide.title_en || '(無標題)' }}</h3>
                <p v-if="slide.title_en" class="text-sm text-gray-600 mt-1">{{ slide.title_en }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ slide.description || slide.description_en || '' }}</p>
                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                  <span>{{ locale === 'en' ? 'Sort' : '排序' }}: {{ slide.sort }}</span>
                  <span :class="slide.status ? 'text-green-600' : 'text-gray-400'">
                    {{ slide.status ? (locale === 'en' ? 'Enabled' : '啟用') : (locale === 'en' ? 'Disabled' : '停用') }}
                  </span>
                  <span v-if="slide.link_url">{{ locale === 'en' ? 'Link' : '連結' }}: {{ slide.link_url }}</span>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="startEdit(slide)"
                  class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 text-sm"
                >
                  {{ locale === 'en' ? 'Edit' : '編輯' }}
                </button>
                <button
                  @click="deleteSlide(slide.id)"
                  class="px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 text-sm"
                >
                  {{ locale === 'en' ? 'Delete' : '刪除' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="slides.length === 0" class="text-center py-12 text-gray-500">
        {{ locale === 'en' ? 'No carousel slides yet. Click "Add Slide" to create one.' : '尚無輪播項目，點擊「新增輪播項目」來建立。' }}
      </div>
    </div>
  </div>
</template>

