<!-- resources/js/Pages/Leads/Create.vue -->
<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  plans: { type: Array, default: () => [] }
})

const form = useForm({
  plan: props.plans[0]?.value ?? '',
  name: '',
  phone: '',
  email: '',
  notes: '',
})

const submit = () => form.post(route('leads.store'))

const page = usePage()
</script>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head title="需求填寫" />

  <!-- 成功訊息 -->
  <div v-if="page.props.flash?.success" class="mb-4 rounded-xl border bg-green-50 text-green-800 px-4 py-3">
    {{ page.props.flash.success }}
  </div>

  <h1 class="text-2xl md:text-3xl font-bold">選擇方案並留下聯絡資料</h1>
  <p class="mt-2 text-gray-600">請選擇您想了解的方案並填寫基本資料，我們將盡快與您聯繫。</p>

  <form @submit.prevent="submit" class="mt-8 grid lg:grid-cols-3 gap-8">
    <!-- 左：方案選擇 -->
    <section class="lg:col-span-1 rounded-2xl border bg-white p-5">
      <h2 class="font-semibold">選擇方案</h2>
      <div v-if="props.plans.length === 0" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
        <p class="text-sm text-yellow-800">目前沒有可用的方案，請稍後再試。</p>
      </div>
      <div v-else class="mt-4 space-y-3">
        <label
          v-for="p in props.plans"
          :key="p.value"
          class="flex items-center gap-3 rounded-xl border p-3 cursor-pointer hover:bg-gray-50"
          :class="form.plan === p.value ? 'border-green-600 ring-1 ring-green-600/20 bg-green-50' : ''"
        >
          <input type="radio" class="accent-green-600" v-model="form.plan" :value="p.value" />
          <span class="font-medium">{{ p.label }}</span>
        </label>
      </div>
      <p v-if="form.errors.plan" class="mt-2 text-sm text-red-600">{{ form.errors.plan }}</p>
    </section>

    <!-- 右：聯絡資料 -->
    <section class="lg:col-span-2 rounded-2xl border bg-white p-5 grid gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">姓名 <span class="text-red-500">*</span></label>
        <input v-model="form.name" type="text" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" placeholder="王小明" />
        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">電話</label>
          <input v-model="form.phone" type="tel" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" placeholder="+886 912 345 678" />
          <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
          <input v-model="form.email" type="email" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" placeholder="you@example.com" />
          <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">需求/備註</label>
        <textarea v-model="form.notes" rows="4" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" placeholder="請簡述您的需求與場域情境"></textarea>
        <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
      </div>

      <div class="pt-2">
        <button
          type="submit"
          :disabled="form.processing || props.plans.length === 0"
          class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-green-600 to-blue-600 text-white px-5 py-2.5 font-medium disabled:opacity-60"
        >
          {{ form.processing ? '送出中…' : '送出' }}
        </button>
      </div>
    </section>
  </form>
</template>


