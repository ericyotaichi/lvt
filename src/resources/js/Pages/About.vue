<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  about: { type: Object, default: null }
})

const page = usePage()
const locale = computed(() => page.props.locale || 'zh')

// 左側章節導覽
const sections = [
  { id: 'intro',   label: '公司介紹' },

  { id: 'contact', label: '聯絡我們' },
]

const active = ref('intro')
let observer
onMounted(() => {
  const els = sections.map(s => document.getElementById(s.id)).filter(Boolean)
  observer = new IntersectionObserver((entries) => {
    const visible = entries
      .filter(e => e.isIntersecting)
      .sort((a, b) => Math.abs(a.boundingClientRect.top) - Math.abs(b.boundingClientRect.top))
    if (visible[0]) active.value = visible[0].target.id
  }, { rootMargin: '-20% 0px -70% 0px', threshold: [0, 0.2, 0.6] })
  els.forEach(el => observer.observe(el))
})
onBeforeUnmount(() => observer?.disconnect())

// 获取公司介绍内容
const introContent = computed(() => {
  return props.about?.content || ''
})

// 获取图片URL
const imageUrl = computed(() => {
  return props.about?.image_url || null
})
</script>

<script>
/* 永續套用共用版型 */
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head title="關於我們" />

  <!-- Hero -->
  <section class="relative overflow-hidden border rounded-3xl bg-gradient-to-br from-green-50 to-blue-50">
    <div class="max-w-7xl mx-auto px-6 py-14 md:py-20">
      <p class="text-sm text-green-700 font-semibold">ABOUT US</p>
      <h1 class="mt-2 text-3xl md:text-5xl font-bold tracking-tight">村源科技股份有限公司</h1>
      <p class="mt-4 max-w-2xl text-gray-600">
        以人為本・健康・節能
      </p>
    </div>
  </section>

  <!-- 內容：左側章節導覽 + 右側內容 -->
  <div class="mt-10 grid md:grid-cols-[240px,1fr] gap-8">
    <!-- 側邊章節導覽（桌機） -->
    <nav class="hidden md:block">
      <ul class="sticky top-28 space-y-1">
        <li v-for="s in sections" :key="s.id">
          <a :href="'#'+s.id"
             class="block px-3 py-2 rounded-xl border text-sm transition hover:bg-white"
             :class="active === s.id ? 'border-green-600 text-green-700 bg-white shadow-sm'
                                     : 'border-transparent text-gray-600'">
            {{ s.label }}
          </a>
        </li>
      </ul>
    </nav>

    <!-- 右側內容 -->
    <div class="space-y-16">
      <!-- 公司介紹 -->
      <section id="intro" class="scroll-mt-28">
        <div class="grid lg:grid-cols-2 gap-8 items-center">
          <div>
            <h2 class="text-2xl md:text-3xl font-bold">公司介紹</h2>
            <p v-if="introContent" class="mt-4 text-gray-600 leading-7 whitespace-pre-line">
              {{ introContent }}
            </p>
            <p v-else class="mt-4 text-gray-600 leading-7">
              村源科技股份有限公司秉持「以人為本、健康、節能」為核心價值，致力於打造兼具永續發展與人本關懷的工程技術解決方案。
            </p>
            <ul class="mt-6 grid sm:grid-cols-2 gap-3 text-sm">
              <li class="p-3 rounded-xl border bg-white">從「人」出發，打造安全舒適的工作與生活環境</li>
              <li class="p-3 rounded-xl border bg-white">從「健康」著眼，優化空氣與環境品質</li>
              <li class="p-3 rounded-xl border bg-white">以「節能」為目標，提升系統效率、減少資源浪費</li>
              <li class="p-3 rounded-xl border bg-white">以「解決問題」為核心，創造具體、可衡量的工程價值</li>
            </ul>
          </div>
          <div>
            <!-- 公司形象圖 -->
            <img 
              v-if="imageUrl" 
              :src="imageUrl" 
              alt="公司介紹" 
              class="aspect-[4/3] rounded-2xl border object-cover w-full"
            />
            <div v-else class="aspect-[4/3] rounded-2xl border bg-gray-100"></div>
          </div>
        </div>
      </section>

      <!-- 願景使命 -->
      <!-- <section id="vision" class="scroll-mt-28">
        <h2 class="text-2xl md:text-3xl font-bold">願景與使命</h2>
        <div class="mt-6 grid md:grid-cols-3 gap-6">
          <article class="rounded-2xl border bg-white p-6">
            <h3 class="font-semibold">願景</h3>
            <p class="mt-2 text-gray-600">（你的願景文案）</p>
          </article>
          <article class="rounded-2xl border bg-white p-6">
            <h3 class="font-semibold">使命</h3>
            <p class="mt-2 text-gray-600">（你的使命文案）</p>
          </article>
          <article class="rounded-2xl border bg-white p-6">
            <h3 class="font-semibold">核心價值</h3>
            <p class="mt-2 text-gray-600">（你的核心價值文案）</p>
          </article>
        </div>
      </section> -->

      <!-- 里程碑 -->
      <!-- <section id="timeline" class="scroll-mt-28">
        <h2 class="text-2xl md:text-3xl font-bold">發展里程</h2>
        <ol class="mt-6 relative border-s pl-6 space-y-6">
          <li>
            <div class="absolute -left-2.5 w-5 h-5 rounded-full bg-green-600"></div>
            <h4 class="font-semibold">2020 — 公司成立</h4>
            <p class="text-gray-600 text-sm mt-1">（替換你的里程碑敘述）</p>
          </li>
          <li>
            <div class="absolute -left-2.5 w-5 h-5 rounded-full bg-green-600"></div>
            <h4 class="font-semibold">2022 — 平台 2.0 上線</h4>
            <p class="text-gray-600 text-sm mt-1">（替換你的里程碑敘述）</p>
          </li>
          <li>
            <div class="absolute -left-2.5 w-5 h-5 rounded-full bg-green-600"></div>
            <h4 class="font-semibold">2024 — 國際合作</h4>
            <p class="text-gray-600 text-sm mt-1">（替換你的里程碑敘述）</p>
          </li>
        </ol>
      </section> -->

      <!-- 關鍵數字 -->
      <!-- <section id="stats" class="scroll-mt-28">
        <h2 class="text-2xl md:text-3xl font-bold">關鍵數字</h2>
        <dl class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="rounded-2xl border bg-white p-5 text-center">
            <dt class="text-sm text-gray-600">累積節能</dt>
            <dd class="mt-1 text-2xl font-bold">15%+</dd>
          </div>
          <div class="rounded-2xl border bg-white p-5 text-center">
            <dt class="text-sm text-gray-600">服務據點</dt>
            <dd class="mt-1 text-2xl font-bold">8 國</dd>
          </div>
          <div class="rounded-2xl border bg-white p-5 text-center">
            <dt class="text-sm text-gray-600">設備連線</dt>
            <dd class="mt-1 text-2xl font-bold">50k+</dd>
          </div>
          <div class="rounded-2xl border bg-white p-5 text-center">
            <dt class="text-sm text-gray-600">SLA</dt>
            <dd class="mt-1 text-2xl font-bold">99.9%</dd>
          </div>
        </dl>
      </section> -->

      <!-- 團隊 -->
      <!-- <section id="team" class="scroll-mt-28">
        <h2 class="text-2xl md:text-3xl font-bold">核心團隊</h2>
        <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <article class="rounded-2xl border bg-white p-5">
            <div class="aspect-square rounded-xl bg-gray-100 mb-4"></div>
            <h3 class="font-semibold">Alex Chen</h3>
            <p class="text-sm text-gray-600">共同創辦人 / CEO</p>
          </article>
          <article class="rounded-2xl border bg-white p-5">
            <div class="aspect-square rounded-xl bg-gray-100 mb-4"></div>
            <h3 class="font-semibold">Ivy Lin</h3>
            <p class="text-sm text-gray-600">CTO</p>
          </article>
          <article class="rounded-2xl border bg-white p-5">
            <div class="aspect-square rounded-xl bg-gray-100 mb-4"></div>
            <h3 class="font-semibold">Ken Wu</h3>
            <p class="text-sm text-gray-600">商務發展</p>
          </article>
        </div>
      </section> -->

      <!-- 夥伴 -->
      <!-- <section id="partners" class="scroll-mt-28">
        <h2 class="text-2xl md:text-3xl font-bold">認證與合作夥伴</h2>
        <div class="mt-6 grid sm:grid-cols-3 lg:grid-cols-5 gap-4">
          <div class="h-16 rounded-xl border bg-white grid place-items-center text-gray-500">ISO 9001</div>
          <div class="h-16 rounded-xl border bg-white grid place-items-center text-gray-500">ISO 27001</div>
          <div class="h-16 rounded-xl border bg-white grid place-items-center text-gray-500">Energy Star</div>
          <div class="h-16 rounded-xl border bg-white grid place-items-center text-gray-500">Partner A</div>
          <div class="h-16 rounded-xl border bg-white grid place-items-center text-gray-500">Partner B</div>
        </div>
      </section> -->

      <!-- CTA -->
      <section id="contact" class="scroll-mt-28">
        <div class="rounded-3xl overflow-hidden border bg-gradient-to-r from-green-600 to-blue-600 text-white p-8 md:p-12">
          <div class="grid md:grid-cols-2 items-center gap-6">
            <div>
              <h3 class="text-2xl font-semibold">想更了解我們？</h3>
              <p class="mt-2 text-white/90">留下需求與聯絡方式，我們將儘速回覆。</p>
            </div>
            <div class="md:text-right">
              <Link href="/lead" class="inline-block px-5 py-2.5 rounded-xl bg-white text-green-700 font-medium hover:bg-gray-100">
                聯絡我們
              </Link>
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>
