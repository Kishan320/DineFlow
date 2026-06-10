<template>
  <section id="testimonials" ref="sectionRef" class="py-24 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Guest Stories</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          What They&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Say.</span>
        </h2>
      </div>

      <div class="lp-reveal lp-glass-card rounded-3xl p-8 md:p-12 relative overflow-hidden lp-glow-gold">
        <!-- Decorative quote mark -->
        <div class="absolute top-6 right-8 lp-font-disp text-9xl leading-none select-none pointer-events-none" style="color:rgba(200,150,90,0.1)">"</div>

        <div class="relative z-10">
          <!-- Stars -->
          <div class="flex gap-1 mb-6">
            <StarIcon v-for="n in current.rating" :key="n" :size="20" fill="#f59e0b" color="#f59e0b" />
          </div>

          <!-- Quote -->
          <blockquote class="lp-font-disp italic text-xl md:text-2xl leading-relaxed mb-8 transition-all duration-500" style="color:var(--lp-fg)">
            "{{ current.quote }}"
          </blockquote>

          <!-- Author -->
          <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-full overflow-hidden shrink-0 border-2" style="border-color:rgba(200,150,90,0.3)">
                <LpImage :src="current.image" :alt="current.alt" class="w-full h-full object-cover" />
              </div>
              <div>
                <p class="font-bold" style="color:var(--lp-fg)">{{ current.name }}</p>
                <p class="text-sm" style="color:var(--lp-muted-fg)">{{ current.role }}</p>
              </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold border" style="background:rgba(200,150,90,0.1);color:var(--lp-primary);border-color:rgba(200,150,90,0.2)">
              {{ current.occasion }}
            </span>
          </div>
        </div>

        <!-- Navigation -->
        <div class="flex items-center justify-between mt-8 pt-6 border-t" style="border-color:var(--lp-border)">
          <div class="flex gap-2">
            <button
              v-for="(_, i) in testimonials"
              :key="i"
              @click="jumpTo(i)"
              :aria-label="`Go to testimonial ${i + 1}`"
              :class="['h-2 rounded-full transition-all duration-300', i === currentIndex ? 'w-8 bg-[var(--lp-primary)]' : 'w-2 hover:opacity-50']"
              :style="i !== currentIndex ? { background: 'var(--lp-border)' } : {}"
            />
          </div>
          <div class="flex gap-2">
            <button @click="prev" class="w-10 h-10 rounded-full border flex items-center justify-center transition-all duration-200" style="border-color:var(--lp-border)" aria-label="Previous">
              <ChevronLeftIcon :size="18" :color="'var(--lp-fg)'" />
            </button>
            <button @click="next" class="w-10 h-10 rounded-full border flex items-center justify-center transition-all duration-200" style="border-color:var(--lp-border)" aria-label="Next">
              <ChevronRightIcon :size="18" :color="'var(--lp-fg)'" />
            </button>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Star as StarIcon, ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';
import LpImage from './LpImage.vue';

const sectionRef = ref(null);
useReveal(sectionRef);

const testimonials = [
  { id: 1, name: 'Priya Nair',            role: 'Food Writer, The Hindu',         image: 'https://img.rocket.new/generatedImages/rocket_gen_img_1ecb4aca8-1763296537332.png',  alt: 'Indian woman portrait', rating: 5, quote: "LuxeDine is the closest I've come to a Michelin-star experience in India. The Rogan Josh alone is worth the trip from Mumbai — complex, soulful, and absolutely unforgettable.", occasion: 'Anniversary Dinner' },
  { id: 2, name: 'Rahul Sharma',           role: 'Managing Director, Apex Ventures', image: 'https://img.rocket.new/generatedImages/rocket_gen_img_1e7bca6b4-1763295393217.png', alt: 'Indian professional man portrait', rating: 5, quote: "We've hosted 12 client dinners at LuxeDine this year. Every single time, our guests leave impressed. The private dining room and attentive service make it our go-to for important meetings.", occasion: 'Business Dining' },
  { id: 3, name: 'Anika Mehta',            role: 'Travel Blogger, Delhi Diaries',  image: 'https://images.unsplash.com/photo-1599029576091-645ffd738b55',                      alt: 'Young Indian woman portrait',      rating: 5, quote: "I've eaten at 200+ restaurants across India. LuxeDine's tasting menu is the single best meal I've had in this country. Chef Arjun's storytelling through food is genuinely moving.", occasion: 'Solo Dining' },
  { id: 4, name: 'Vikram & Sunita Khanna', role: 'Regulars since 2012',            image: 'https://images.unsplash.com/photo-1581704723043-70c2216277de',                      alt: 'Indian couple smiling',            rating: 5, quote: "We celebrate every anniversary here. Twelve years, twelve dinners, and each one better than the last. The staff remembers our preferences and the ambiance is simply magical.", occasion: 'Anniversary Tradition' },
];

const currentIndex = ref(0);
const current = computed(() => testimonials[currentIndex.value]);

let timer = null;

function next() { resetTimer(() => { currentIndex.value = (currentIndex.value + 1) % testimonials.length; }); }
function prev() { resetTimer(() => { currentIndex.value = (currentIndex.value - 1 + testimonials.length) % testimonials.length; }); }
function jumpTo(i) { resetTimer(() => { currentIndex.value = i; }); }

function resetTimer(fn) {
  clearInterval(timer);
  fn();
  timer = setInterval(() => { currentIndex.value = (currentIndex.value + 1) % testimonials.length; }, 5000);
}

onMounted(() => { timer = setInterval(() => { currentIndex.value = (currentIndex.value + 1) % testimonials.length; }, 5000); });
onUnmounted(() => clearInterval(timer));
</script>
