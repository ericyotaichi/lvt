<script setup>
import { ref } from 'vue'
const form = ref({ name:'', email:'', phone:'', company:'', topic_id:'', message:'', identity_type:'', source_page: window.location.pathname })
const loading = ref(false), ok = ref(false), error = ref('')
async function submit(){
  loading.value = true; error.value = ''
  try{
    const res = await fetch('/lead', {
      method:'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify(form.value)
    })
    if(!res.ok) throw new Error('提交失敗')
    ok.value = true
  }catch(e){ error.value = e.message } finally { loading.value = false }
}
</script>
<template>
  <form @submit.prevent="submit" class="space-y-4">
    <div class="grid sm:grid-cols-2 gap-4">
      <input v-model="form.name" class="input" placeholder="姓名" required />
      <input v-model="form.email" type="email" class="input" placeholder="Email" required />
      <input v-model="form.phone" class="input sm:col-span-1" placeholder="電話（選填）" />
      <input v-model="form.company" class="input sm:col-span-1" placeholder="公司（選填）" />
      <select v-model="form.topic_id" class="input sm:col-span-2">
        <option value="">選擇有興趣的主題</option>
        <option value="1">產品諮詢</option>
        <option value="2">技術合作</option>
      </select>
    </div>
    <textarea v-model="form.message" class="input h-32" placeholder="請描述您的需求…" required></textarea>
    <button :disabled="loading" class="btn btn-primary w-full sm:w-auto">
      {{ loading ? '提交中…' : '送出' }}
    </button>
    <p v-if="ok" class="text-green-600 text-sm">已收到，受保護頁面已解鎖。</p>
    <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
  </form>
</template>
<style scoped>
.input{ @apply w-full rounded-xl border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white; }
.btn{ @apply rounded-xl px-4 py-2 font-medium; }
.btn-primary{ @apply bg-green-600 text-white hover:bg-green-700; }
</style>
