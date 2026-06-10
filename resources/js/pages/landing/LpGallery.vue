<template>
  <section id="gallery" ref="sectionRef" class="py-24 px-4 sm:px-6" style="background:rgba(240,234,224,0.3)">
    <div class="max-w-7xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Visual Feast</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          The&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Gallery.</span>
        </h2>
      </div>

      <!-- Masonry Grid -->
      <div class="lp-reveal lp-masonry-grid">
        <div
          v-for="item in galleryItems"
          :key="item.id"
          @click="lightbox = item"
          :class="['relative overflow-hidden rounded-2xl cursor-pointer group', item.span]"
          :style="{ minHeight: item.span === 'lp-masonry-tall' ? '320px' : '200px' }"
        >
          <LpImage :src="item.src" :alt="item.alt" :fill="true" class="object-cover group-hover:scale-105 transition-transform duration-700" />
          <!-- Hover overlay -->
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-12 h-12 rounded-full lp-glass-card flex items-center justify-center">
              <ZoomInIcon :size="22" color="white" />
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Lightbox -->
    <Teleport to="body">
      <div
        v-if="lightbox"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.9);backdrop-filter:blur(4px)"
        @click="lightbox = null"
      >
        <button class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center text-white transition-colors" style="background:rgba(255,255,255,0.1)" aria-label="Close">
          <XIcon :size="20" color="white" />
        </button>
        <div class="relative max-w-4xl w-full max-h-[85vh] rounded-2xl overflow-hidden" @click.stop>
          <img :src="lightbox.src" :alt="lightbox.alt" class="w-full h-auto object-contain" />
        </div>
      </div>
    </Teleport>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { ZoomIn as ZoomInIcon, X as XIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';
import LpImage from './LpImage.vue';

const sectionRef = ref(null);
useReveal(sectionRef, { threshold: 0.05 });

const lightbox = ref(null);

const galleryItems = [
  { id: 1, src: 'https://img.rocket.new/generatedImages/rocket_gen_img_186321a6e-1772087389567.png',  alt: 'Elegant restaurant dining room',           span: 'lp-masonry-tall' },
  { id: 2, src: 'https://img.rocket.new/generatedImages/rocket_gen_img_1fcffea7f-1772156505295.png',  alt: 'Colorful Indian thali top-down view',       span: 'lp-masonry-wide' },
  { id: 3, src: 'https://img.rocket.new/generatedImages/rocket_gen_img_1791b975e-1772056118364.png',  alt: 'Intimate private dining room',              span: '' },
  { id: 4, src: 'https://img.rocket.new/generatedImages/rocket_gen_img_15df498c3-1780547421918.png',  alt: 'Chef carefully plating a dish',             span: 'lp-masonry-tall' },
  { id: 5, src: 'https://img.rocket.new/generatedImages/rocket_gen_img_190ccd77e-1772204466828.png',  alt: 'Artistically plated fine dining dish',      span: '' },
  { id: 6, src: 'https://images.unsplash.com/photo-1709364937384-1f30a49a8269',                       alt: 'Multiple small plates on marble table',     span: 'lp-masonry-wide' },
  { id: 7, src: 'https://images.unsplash.com/photo-1667470256325-5e55a6a3b7cf',                       alt: 'Elegant dessert plating',                   span: '' },
  { id: 8, src: 'https://img.rocket.new/generatedImages/rocket_gen_img_1feffe525-1773087928627.png',  alt: 'Golden mango lassi in tall glass',          span: 'lp-masonry-tall' },
];

function onKeydown(e) { if (e.key === 'Escape') lightbox.value = null; }
onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => window.removeEventListener('keydown', onKeydown));
</script>
