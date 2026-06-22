<template>
  <section class="relative min-h-screen flex items-center overflow-hidden">
    <!-- Parallax Background -->
    <div ref="bgRef" class="absolute inset-0 z-0" style="transform-origin:top center">
      <LpImage
        src="https://img.rocket.new/generatedImages/rocket_gen_img_1517867ae-1774737643947.png"
        alt="Elegant fine dining restaurant interior with warm amber lighting"
        :fill="true"
        :priority="true"
        class="object-cover w-full h-full"
      />
      <div class="absolute inset-0 lp-hero-scrim" />
      <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,0.6) 0%,transparent 50%,rgba(0,0,0,0.2) 100%)" />
    </div>

    <!-- Content -->
    <div ref="contentRef" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 pt-28 pb-20 w-full">
      <div class="grid lg:grid-cols-2 gap-12 items-center">

        <!-- Left: Text -->
        <div class="space-y-7">
          <div class="hero-anim inline-flex items-center gap-2 px-4 py-2 rounded-full lp-glass-card">
            <span class="w-2 h-2 rounded-full bg-[var(--lp-primary)] animate-pulse" />
            <span class="text-xs font-bold tracking-[0.25em] uppercase text-white/90">New Delhi's Finest Table</span>
          </div>

          <h1 class="hero-anim lp-text-hero lp-font-disp font-medium leading-[0.92] tracking-tight text-white">
            A Feast for&nbsp;<span class="lp-shimmer lp-font-disp italic">the Senses.</span>
          </h1>

          <p class="hero-anim text-lg text-white/75 max-w-lg leading-relaxed font-medium">
            From the tandoors of Punjab to the coastal kitchens of Kerala — every plate at DineFlow tells a story centuries in the making.
          </p>

          <div class="hero-anim flex flex-wrap gap-4">
            <button @click="scrollTo('#menu')" class="lp-btn-primary px-7 py-3.5 text-base gap-2">
              <BookOpenIcon :size="18" color="white" />
              Explore Menu
            </button>
            <button
              @click="scrollTo('#reservation')"
              class="lp-btn-outline px-7 py-3.5 text-base gap-2"
              style="border-color:rgba(255,255,255,0.4);color:white"
            >
              <CalendarIcon :size="18" color="white" />
              Reserve a Table
            </button>
          </div>
        </div>

        <!-- Right: Floating Stat Cards (desktop) -->
        <div class="hidden lg:flex flex-col gap-4 items-end">
          <div
            v-for="(stat, i) in floatingStats"
            :key="stat.label"
            :class="['lp-glass-card rounded-2xl px-6 py-4 flex items-center gap-4 min-w-52', i === 1 ? 'lp-float' : i === 2 ? 'lp-float-delayed' : '']"
          >
            <div class="w-12 h-12 rounded-xl lp-gradient-bg-gold flex items-center justify-center shrink-0">
              <component :is="stat.icon" :size="22" color="white" />
            </div>
            <div>
              <p class="text-2xl lp-font-disp font-semibold text-white">{{ stat.value }}</p>
              <p class="text-xs text-white/65 font-medium tracking-wide">{{ stat.label }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Stats Row -->
      <div class="lg:hidden mt-10 grid grid-cols-3 gap-3">
        <div v-for="stat in floatingStats" :key="stat.label" class="lp-glass-card rounded-xl p-3 text-center">
          <p class="text-xl lp-font-disp font-semibold text-white">{{ stat.value }}</p>
          <p class="text-[10px] text-white/65 font-medium mt-0.5">{{ stat.label }}</p>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2 opacity-60">
      <span class="text-xs text-white/70 tracking-widest uppercase">Scroll</span>
      <div class="w-px h-10 bg-gradient-to-b from-white/50 to-transparent" />
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { BookOpen as BookOpenIcon, Calendar as CalendarIcon, Smile as SmileIcon, Sparkles as SparklesIcon, Trophy as TrophyIcon } from 'lucide-vue-next';
import LpImage from './LpImage.vue';

const bgRef      = ref(null);
const contentRef = ref(null);

const floatingStats = [
  { value: '12K+',   label: 'Happy Guests',    icon: SmileIcon },
  { value: '200+',   label: 'Premium Dishes',  icon: SparklesIcon },
  { value: '18 Yrs', label: 'Of Excellence',   icon: TrophyIcon },
];

function scrollTo(href) {
  document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
}

function onScroll() {
  if (bgRef.value) bgRef.value.style.transform = `translateY(${window.scrollY * 0.35}px)`;
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true });

  // Stagger entrance animations
  const els = contentRef.value?.querySelectorAll('.hero-anim');
  els?.forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(40px)';
    setTimeout(() => {
      el.style.transition = `opacity 1s cubic-bezier(0.16,1,0.3,1) ${i * 150}ms, transform 1s cubic-bezier(0.16,1,0.3,1) ${i * 150}ms`;
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, 100);
  });
});

onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>
