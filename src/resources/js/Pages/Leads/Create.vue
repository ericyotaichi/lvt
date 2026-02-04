<!-- resources/js/Pages/Leads/Create.vue -->
<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  plans: { type: Array, default: () => [] },
  contactSetting: { type: Object, default: null }
})

const page = usePage()
const locale = computed(() => page.props.locale || 'zh')

const text = computed(() => {
  if (locale.value === 'en') {
    return {
      title: 'Contact Request',
      heroAlt: 'Contact Us',
      heading: 'Select a plan and leave your contact details',
      subheading: 'Choose the solution you want to learn about and share your contact info. We will get back to you soon.',
      selectPlan: 'Select Plan',
      otherOption: 'Other',
      nameLabel: 'Name',
      namePlaceholder: 'John Doe',
      phoneLabel: 'Phone',
      phonePlaceholder: '+1 650 123 4567',
      notesLabel: 'Needs / Notes',
      notesPlaceholder: 'Briefly describe your needs and scenario',
      submit: 'Submit',
      submitting: 'Submitting…',
    }
  }
  return {
    title: '需求填寫',
    heroAlt: '聯絡我們',
    heading: '選擇方案並留下聯絡資料',
    subheading: '請選擇您想了解的方案並填寫基本資料，我們將盡快與您聯繫。',
    selectPlan: '選擇方案',
    otherOption: '其他',
    nameLabel: '姓名',
    namePlaceholder: '王小明',
    phoneLabel: '電話',
    phonePlaceholder: '+886 912 345 678',
    notesLabel: '需求/備註',
    notesPlaceholder: '請簡述您的需求與場域情境',
    submit: '送出',
    submitting: '送出中…',
  }
})

const planOptions = computed(() => {
  const list = [...(props.plans || [])]
  // 始終在最下方添加「其他」選項
  list.push({ value: 'other', label: text.value.otherOption })
  return list
})

const form = useForm({
  plan: props.plans?.[0]?.value ?? 'other',
  name: '',
  phone: '',
  email: '',
  notes: '',
})

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      alert(flash.success)
    }
    if (flash?.error) {
      alert(flash.error)
    }
  },
  { immediate: true }
)

const submit = () => {
  form.post(route('leads.store'), {
    preserveScroll: true,
    onError: (errors) => {
      const msg = errors?.error || Object.values(errors || {}).find(Boolean)
      if (msg) alert(msg)
    },
  })
}
</script>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>

<template>
  <Head :title="text.title" />

  <section v-if="props.contactSetting?.hero_image_url" class="mb-8 -mx-6 md:-mx-0">
    <div class="w-full rounded-3xl overflow-hidden border">
      <img :src="props.contactSetting.hero_image_url" :alt="text.heroAlt" class="w-full h-full object-cover max-h-[420px]" />
    </div>
  </section>

  <h1 class="text-2xl md:text-3xl font-bold">{{ text.heading }}</h1>
  <p class="mt-2 text-gray-600">{{ text.subheading }}</p>

  <form @submit.prevent="submit" class="mt-8 grid lg:grid-cols-3 gap-8">
    <!-- 左：方案選擇 -->
    <section class="lg:col-span-1 rounded-2xl border bg-white p-5">
      <h2 class="font-semibold">{{ text.selectPlan }}</h2>
      <div class="mt-4 space-y-3">
        <label
          v-for="p in planOptions"
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
        <label class="block text-sm font-medium text-gray-700">{{ text.nameLabel }} <span class="text-red-500">*</span></label>
        <input v-model="form.name" type="text" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" :placeholder="text.namePlaceholder" />
        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">{{ text.phoneLabel }}</label>
          <input v-model="form.phone" type="tel" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" :placeholder="text.phonePlaceholder" />
          <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
          <input v-model="form.email" type="email" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" placeholder="you@example.com" />
          <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">{{ text.notesLabel }}</label>
        <textarea v-model="form.notes" rows="4" class="mt-1 w-full rounded-xl border px-3 py-2 focus:ring-2 focus:ring-green-600/30" :placeholder="text.notesPlaceholder"></textarea>
        <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
      </div>

      <div class="pt-2">
        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-green-600 to-blue-600 text-white px-5 py-2.5 font-medium disabled:opacity-60"
        >
          {{ form.processing ? text.submitting : text.submit }}
        </button>
      </div>
    </section>
  </form>
</template>


