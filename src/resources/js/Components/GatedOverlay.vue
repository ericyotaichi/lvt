<script setup>
const props = defineProps({ show: Boolean })
</script>
<template>
  <div v-if="show" class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full space-y-3">
      <h3 class="text-xl font-semibold">此內容需先填寫表單</h3>
      <p class="text-gray-600">完成聯絡表單後，即可瀏覽案例與應用場域。</p>
      <a :href="'/lead?redirect=' + encodeURIComponent(location.pathname+location.search)" class="btn btn-primary w-full text-center">前往填寫</a>
    </div>
  </div>
</template>
<style scoped>
.btn{ @apply rounded-xl px-4 py-2 font-medium bg-green-600 text-white hover:bg-green-700; }
</style>
