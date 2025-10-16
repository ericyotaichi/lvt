<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import round1 from '@/assets/images/round1.jpg'
import round2 from '@/assets/images/round2.jpg'
import round3 from '@/assets/images/round3.jpg'

const modules = [Navigation, Pagination, Autoplay];

const page = usePage();
const slides = computed(() => page.props.carouselSlides || []);

// 默认轮播数据（如果没有数据库数据时使用）
const defaultSlides = [
  {
    title: '節能 · 環保 · 智慧轉型',
    description: '以核心技術驅動產業轉型，打造高效、可持續的智慧解決方案。',
    image_url: round1,
    link_url: null,
    link_text: null,
  },
  {
    title: '核心技術賦能',
    description: 'IoT · AI/ML · 資料平台',
    image_url: round2,
    link_url: null,
    link_text: null,
  },
  {
    title: '共創機遇',
    description: '攜手合作，共同推動綠色未來',
    image_url: round3,
    link_url: null,
    link_text: null,
  },
];

const displaySlides = computed(() => {
  if (slides.value && slides.value.length > 0) {
    return slides.value;
  }
  return defaultSlides;
});
</script>

<template>
  <section class="w-full h-[40vh] md:h-[40vh] -mx-0 md:-mx-0">
    <Swiper 
      v-if="displaySlides.length > 0"
      :modules="modules" 
      :loop="displaySlides.length > 1" 
      :autoplay="{ delay: 4000, disableOnInteraction: false }"
      :pagination="{ clickable: true }" 
      navigation 
      class="h-full"
    >
      <SwiperSlide v-for="(slide, index) in displaySlides" :key="slide.id || index">
        <div class="relative h-full">
          <img 
            :src="slide.image_url" 
            :alt="slide.title || ''" 
            class="absolute inset-0 w-full h-full object-cover" 
          />
          <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-center text-white">
            <div class="max-w-2xl px-6">
              <h1 v-if="slide.title" class="text-4xl md:text-6xl font-bold">{{ slide.title }}</h1>
              <p v-if="slide.description" class="mt-4 text-lg text-white/90">{{ slide.description }}</p>
              <a 
                v-if="slide.link_url && slide.link_text"
                :href="slide.link_url"
                class="mt-6 inline-block px-6 py-3 rounded-xl bg-white text-green-700 font-semibold hover:bg-gray-100"
              >
                {{ slide.link_text }}
              </a>
            </div>
          </div>
        </div>
      </SwiperSlide>
    </Swiper>
  </section>
</template>
