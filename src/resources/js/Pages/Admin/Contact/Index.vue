<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  setting: { type: Object, default: null }
})

const page = usePage()

const form = useForm({
  hero_image_url: props.setting?.hero_image_url ?? '',
  hero_image: null,
})

const heroPreview = ref(props.setting?.hero_image_url ?? null)

const handleHeroChange = (event) => {
  const file = event.target.files?.[0] ?? null
  form.hero_image = file

  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      heroPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  } else {
    heroPreview.value = props.setting?.hero_image_url ?? null
  }
}

const submit = () => {
  form.post(route('admin.contact.update'), {
    forceFormData: true,
    onSuccess: () => {
      form.reset('hero_image')
    }
  })
}
</script>

<script>
export default { layout: AppLayout }
</script>

<template>
  <Head title="聯絡我們設定" />

  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold mb-6">聯絡我們設定</h1>
    <p class="text-gray-600 mb-8">上傳大圖作為聯絡我們頁面的橫幅，顯示於原有內容上方。</p>

    <div v-if="page.props.flash?.success" class="mb-4 rounded-xl border bg-green-50 text-green-800 px-4 py-3">
      {{ page.props.flash.success }}
    </div>

    <form @submit.prevent="submit" class="space-y-6 rounded-2xl border bg-white p-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          橫幅圖片
        </label>
        <input
          type="file"
          accept="image/*"
          class="w-full rounded-xl border px-3 py-2"
          @change="handleHeroChange"
        />
        <p v-if="form.errors.hero_image" class="text-sm text-red-600 mt-1">{{ form.errors.hero_image }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          或者直接輸入圖片網址
        </label>
        <input
          v-model="form.hero_image_url"
          type="text"
          class="w-full rounded-xl border px-3 py-2"
          placeholder="https://example.com/banner.jpg"
        />
        <p v-if="form.errors.hero_image_url" class="text-sm text-red-600 mt-1">{{ form.errors.hero_image_url }}</p>
      </div>

      <div v-if="heroPreview" class="space-y-2">
        <p class="text-sm text-gray-500">預覽（建議比例 16:9 或可視滿版圖片）：</p>
        <div class="aspect-[16/9] rounded-2xl border overflow-hidden bg-gray-100">
          <img :src="heroPreview" alt="Hero preview" class="w-full h-full object-cover" />
        </div>
      </div>

      <div class="flex justify-end">
        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center justify-center rounded-xl bg-green-600 text-white px-5 py-2.5 font-medium hover:bg-green-700 disabled:opacity-60"
        >
          {{ form.processing ? '儲存中…' : '儲存設定' }}
        </button>
      </div>
    </form>
  </div>
</template>
