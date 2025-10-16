<script setup>
const model = defineModel({ type: String, default: '' })
const options = [
  { value: 'enterprise', label: '企業' },
  { value: 'individual', label: '個人' },
  { value: 'partner', label: '合作單位' },
  { value: 'other', label: '其他' },
]
</script>
<template>
  <div class="space-y-2">
    <label class="font-medium">您的身分</label>
    <div class="grid grid-cols-2 gap-2">
      <button v-for="opt in options" :key="opt.value" type="button" @click="model = opt.value"
        class="rounded-xl border px-3 py-2" :class="model===opt.value ? 'border-green-600 bg-green-50' : 'border-gray-300'">
        {{ opt.label }}
      </button>
    </div>
  </div>
</template>
