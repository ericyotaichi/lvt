<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()
const currentLocale = computed(() => page.props.locale || 'zh')

const languages = [
  { code: 'zh', label: '中文', flag: '🇹🇼' },
  { code: 'en', label: 'English', flag: '🇺🇸' },
]

const switchLanguage = (locale) => {
  // 直接构建 URL，使用当前页面的 origin 和简单的路径
  const url = new URL('/language/' + locale, window.location.origin)
  window.location.href = url.toString()
}
</script>

<template>
  <div class="relative inline-block">
    <div class="flex items-center gap-2">
      <button
        v-for="lang in languages"
        :key="lang.code"
        @click="switchLanguage(lang.code)"
        class="px-3 py-1.5 rounded-lg text-sm font-medium transition"
        :class="currentLocale === lang.code
          ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
          : 'text-gray-600 hover:bg-gray-100'"
      >
        <span class="mr-1">{{ lang.flag }}</span>
        {{ lang.label }}
      </button>
    </div>
  </div>
</template>

