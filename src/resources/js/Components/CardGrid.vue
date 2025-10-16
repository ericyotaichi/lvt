<script setup>
defineProps({ items: { type:Array, default:()=>[] } })
</script>
<template>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <article v-for="it in items" :key="it.slug" class="rounded-2xl border p-4 hover:shadow">
      <h3 class="text-lg font-semibold mb-2">{{ it.title }}</h3>
      <p class="text-gray-600 line-clamp-3" v-html="it.excerpt || ''"></p>
      <a :href="`/${it.type}/${it.slug}`" class="text-blue-600 mt-3 inline-block">了解更多 →</a>
    </article>
  </div>
</template>
