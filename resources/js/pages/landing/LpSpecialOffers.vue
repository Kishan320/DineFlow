<template>
  <section id="offers" ref="sectionRef" class="py-24 px-4 sm:px-6" style="background:rgba(240,234,224,0.3)">
    <div class="max-w-7xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Exclusive Offers</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Special&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Experiences.</span>
        </h2>
      </div>

      <div class="grid md:grid-cols-3 gap-6">
        <div
          v-for="(offer, i) in offers"
          :key="offer.id"
          class="lp-reveal relative overflow-hidden rounded-2xl group cursor-pointer"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <!-- Background Image -->
          <div class="aspect-[3/4] relative overflow-hidden">
            <LpImage :src="offer.image" :alt="offer.alt" :fill="true" class="object-cover group-hover:scale-105 transition-transform duration-700" />
            <div class="absolute inset-0" :style="`background:linear-gradient(to top, ${offer.accent}, transparent)`" />
            <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 50%, transparent 100%)" />
          </div>

          <!-- Content Overlay -->
          <div class="absolute inset-0 flex flex-col justify-between p-6">
            <div class="flex items-start justify-between">
              <span class="px-3 py-1 rounded-full text-xs font-bold text-white shadow" style="background:var(--lp-primary)">{{ offer.badge }}</span>
              <span class="px-3 py-1 rounded-full text-xs font-bold text-white border" style="background:rgba(255,255,255,0.2);backdrop-filter:blur(4px);border-color:rgba(255,255,255,0.3)">{{ offer.discount }}</span>
            </div>
            <div class="space-y-3">
              <h3 class="text-xl lp-font-disp font-semibold text-white leading-tight">{{ offer.title }}</h3>
              <p class="text-sm text-white/75 leading-relaxed">{{ offer.description }}</p>
              <button @click="scrollTo('#reservation')" class="flex items-center gap-2 text-sm font-bold text-white group-hover:text-[var(--lp-accent)] transition-colors duration-200">
                {{ offer.cta }}
                <ArrowRightIcon :size="16" class="group-hover:translate-x-1 transition-transform duration-200" />
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { ArrowRight as ArrowRightIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';
import LpImage from './LpImage.vue';

const sectionRef = ref(null);
useReveal(sectionRef);

function scrollTo(href) {
  document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
}

const offers = [
  { id: 1, badge: 'Weekend Special',  title: 'Couples Dining Experience', description: 'A curated 5-course tasting menu for two with a complimentary bottle of sparkling wine. Fridays & Saturdays, 7pm onwards.', discount: '20% Off',   image: 'https://ik.imagekit.io/dineflowimages/landing/image-22.jpg',                          alt: 'Romantic restaurant table',   cta: 'Book for Two',    accent: 'rgba(120,53,15,0.8)' },
  { id: 2, badge: 'Lunch Offer',       title: 'The Power Lunch',           description: 'Weekday business lunch — 3 courses, still water, and filter coffee. Served in 45 minutes or your next visit is complimentary.', discount: '₹999 Only', image: 'https://ik.imagekit.io/dineflowimages/landing/image-23.jpg',                          alt: 'Elegant business lunch',       cta: 'Reserve Lunch',   accent: 'rgba(41,37,36,0.8)' },
  { id: 3, badge: "Chef's Table",      title: 'Private Dining Experience', description: "An exclusive 8-course dinner at Chef Arjun's private table. Limited to 6 guests. Includes a kitchen tour and signed menu.",     discount: 'By Invite', image: 'https://ik.imagekit.io/dineflowimages/landing/image-24.png', alt: "Private chef's table",         cta: 'Enquire Now',     accent: 'rgba(24,24,27,0.8)' },
];
</script>
