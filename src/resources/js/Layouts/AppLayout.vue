<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import logo from '@/assets/images/logo.png'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'

/** 讀取 Inertia 共用屬性 */
const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)
const locale = computed(() => page.props.locale || 'zh')

/** 根據語言動態生成選單 */
const navItems = computed(() => {
  const items = {
    zh: [
      { label: '首頁', href: '/' },
      { label: '關於我們', href: '/about' },
      { label: '核心技術', href: '/tech' },
      { label: '產品與服務', href: '/products' },
      { label: '應用場域', href: '/applications' },
      { label: '案例說明', href: '/cases' },
      { label: '聯絡我們', href: '/lead' },
    ],
    en: [
      { label: 'Home', href: '/' },
      { label: 'About Us', href: '/about' },
      { label: 'Core Technology', href: '/tech' },
      { label: 'Products & Services', href: '/products' },
      { label: 'Applications', href: '/applications' },
      { label: 'Case Studies', href: '/cases' },
      { label: 'Contact Us', href: '/lead' },
    ],
  }
  return items[locale.value] || items.zh
})

/** 目前 URL 與 active 判斷 */
const currentUrl = computed(() => page.url || '/')
const isActive = (href) =>
  currentUrl.value === href || (href !== '/' && currentUrl.value.startsWith(href))

/** 行動選單開關 */
const open = ref(false)
const close = () => (open.value = false)

/** 頁尾內容 */
const footerContent = computed(() => {
  const footer = page.props.footer || '© ' + new Date().getFullYear() + ' YourCompany. All rights reserved.'
  // 确保 footer 是字符串
  const footerStr = typeof footer === 'string' ? footer : String(footer || '')
  // 替换 {{year}} 占位符，并处理换行符
  return footerStr.replace(/\{\{year\}\}/g, new Date().getFullYear()).replace(/\n/g, '<br>')
})
</script>

<template>
  <div class="min-h-dvh bg-gray-50">
    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
      <div class="max-w-7xl mx-auto px-4 md:px-12 lg:px-16 xl:px-24 h-16 flex items-center justify-between">
        <!-- 左：Logo / 名稱 -->
        <Link href="/" class="flex items-center gap-2" @click="close">
          <div class="size-8 md:size-10 lg:size-12">
            <img :src="logo" class="w-full h-full object-contain" />
          </div>
          <span v-if="locale === 'en'" class="font-semibold text-sm md:text-base lg:text-lg tracking-wide whitespace-nowrap">
            Chun Yuan
          </span>
          <span v-else class="font-semibold text-base md:text-xl lg:text-2xl tracking-wide">
            村源科技
          </span>
        </Link>

        <!-- 中：桌機選單 -->
        <nav class="hidden md:flex items-center gap-1">
          <!-- 基本選單 -->
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="px-3 py-2 rounded-xl text-sm transition"
            :class="isActive(item.href)
              ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
              : 'text-gray-600 hover:bg-gray-100'">
            {{ item.label }}
          </Link>

          <!-- 登入後顯示的「編輯文章」 -->
          <Link
            v-if="user"
            :href="route('admin.articles.index')"
            class="ml-1 px-3 py-2 rounded-xl text-sm transition bg-gray-900 text-white hover:bg-gray-800">
            {{ locale === 'en' ? 'Edit Articles' : '編輯文章' }}
          </Link>
        </nav>

        <!-- 右：語言切換 + 行動版漢堡 -->
        <div class="flex items-center gap-3">
          <!-- 語言切換（所有尺寸） -->
          <LanguageSwitcher />

          <!-- 行動版漢堡 -->
          <button
            class="md:hidden inline-flex items-center justify-center rounded-lg p-2.5
                   text-gray-700 hover:text-gray-900 hover:bg-gray-100
                   focus:outline-none focus:ring-2 focus:ring-green-600/40 focus:ring-offset-2"
            @click="open = !open"
            :aria-expanded="open"
            :aria-label="open ? 'Close menu' : 'Open menu'">
            <svg viewBox="0 0 24 24" class="size-6" fill="none">
              <path v-if="!open" d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
              <path v-else d="M6 6l12 12M18 6l-12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
          </button>
        </div>
      </div>

      <!-- 行動版選單 -->
      <transition name="fade">
        <div v-show="open" class="md:hidden border-t bg-white">
          <nav class="px-4 py-3 space-y-1">
            <Link
              v-for="item in navItems"
              :key="item.href"
              :href="item.href"
              class="block px-3 py-2 rounded-lg"
              :class="isActive(item.href)
                ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                : 'text-gray-700 hover:bg-gray-100'"
              @click="close">
              {{ item.label }}
            </Link>

            <!-- 行動版的「編輯文章」 -->
            <Link
              v-if="user"
              :href="route('admin.articles.index')"
              class="block px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800"
              @click="close">
              {{ locale === 'en' ? 'Edit Articles' : '編輯文章' }}
            </Link>

            <!-- 行動版的語言切換 -->
            <div class="pt-2 border-t mt-2">
              <LanguageSwitcher />
            </div>
          </nav>
        </div>
      </transition>
    </header>

    <!-- 內容 -->
    <main class="px-0 md:px-12 lg:px-16 xl:px-24 md:max-w-7xl md:mx-auto py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t bg-white">
      <div class="max-w-7xl mx-auto px-6 md:px-12 lg:px-16 xl:px-24 py-8 text-sm text-gray-500" v-html="footerContent"></div>
    </footer>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; }
</style>
