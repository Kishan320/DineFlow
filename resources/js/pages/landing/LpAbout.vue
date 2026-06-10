<template>
  <section id="about" ref="sectionRef" class="py-24 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="text-center mb-16 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Our Story</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Passion on Every&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Plate.</span>
        </h2>
      </div>

      <div class="grid lg:grid-cols-2 gap-16 items-center mb-20">

        <!-- Image Grid -->
        <div class="lp-reveal grid grid-cols-2 gap-4">
          <div class="space-y-4">
            <div class="rounded-2xl overflow-hidden aspect-[3/4] shadow-lg">
              <LpImage src="https://images.unsplash.com/photo-1543340713-0f7f0abf9e49" alt="Beautifully plated fine dining dish" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
            </div>
            <div class="rounded-2xl overflow-hidden aspect-square shadow-lg">
              <LpImage src="https://img.rocket.new/generatedImages/rocket_gen_img_1076a5b80-1774103238331.png" alt="Restaurant dining room with warm candlelit ambiance" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
            </div>
          </div>
          <div class="space-y-4 pt-10">
            <div class="rounded-2xl overflow-hidden aspect-square shadow-lg">
              <LpImage src="https://img.rocket.new/generatedImages/rocket_gen_img_17aa23708-1772214570411.png" alt="Chef garnishing a dish" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
            </div>
            <div class="rounded-2xl overflow-hidden aspect-[3/4] shadow-lg">
              <LpImage src="https://img.rocket.new/generatedImages/rocket_gen_img_1fcffea7f-1772156505295.png" alt="Artisan Indian thali" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
            </div>
          </div>
        </div>

        <!-- Text Content -->
        <div class="space-y-8">
          <div class="lp-reveal lp-stagger-1">
            <p class="text-lg leading-relaxed mb-6" style="color:var(--lp-muted-fg)">
              Founded in 2006 by Chef Arjun Malhotra and his wife Priya, LuxeDine began as a humble 12-table restaurant in Connaught Place. Today, it stands as New Delhi's most celebrated fine-dining destination — a place where culinary tradition meets contemporary artistry.
            </p>
            <p class="text-lg leading-relaxed" style="color:var(--lp-muted-fg)">
              Our kitchen sources ingredients from 40+ partner farms across India, ensuring that every spice, every vegetable, and every cut of meat carries the integrity of its origin directly to your table.
            </p>
          </div>

          <!-- Chef Quote Card -->
          <div class="lp-reveal lp-stagger-2 lp-glass-card rounded-2xl p-6 flex items-center gap-5">
            <div class="w-16 h-16 rounded-full overflow-hidden shrink-0 border-2" style="border-color:rgba(200,150,90,0.3)">
              <LpImage src="https://img.rocket.new/generatedImages/rocket_gen_img_1bce5c231-1772182221732.png" alt="Head chef portrait" class="w-full h-full object-cover" />
            </div>
            <div>
              <p class="lp-font-disp italic text-lg" style="color:var(--lp-primary)">"Cooking is remembering who you are."</p>
              <p class="text-sm font-semibold mt-1" style="color:var(--lp-fg)">Chef Arjun Malhotra</p>
              <p class="text-xs" style="color:var(--lp-muted-fg)">Founder & Executive Chef, LuxeDine</p>
            </div>
          </div>

          <!-- Why Us Grid -->
          <div class="lp-reveal lp-stagger-3 grid grid-cols-2 gap-4">
            <div v-for="item in whyUs" :key="item.title" class="flex gap-3 items-start">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5" style="background:rgba(200,150,90,0.1)">
                <component :is="item.icon" :size="18" style="color:var(--lp-primary)" />
              </div>
              <div>
                <p class="font-semibold text-sm" style="color:var(--lp-fg)">{{ item.title }}</p>
                <p class="text-xs mt-0.5 leading-relaxed" style="color:var(--lp-muted-fg)">{{ item.desc }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { Sparkles as SparklesIcon, Leaf as LeafIcon, Trophy as TrophyIcon, Heart as HeartIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';
import LpImage from './LpImage.vue';

const sectionRef = ref(null);
useReveal(sectionRef);

const whyUs = [
  { icon: SparklesIcon, title: 'Heritage Recipes',  desc: 'Dishes passed down through five generations of master chefs.' },
  { icon: LeafIcon,     title: 'Farm to Table',     desc: 'Ingredients sourced fresh daily from certified organic farms.' },
  { icon: TrophyIcon,   title: '9 National Awards', desc: "Recognized as Delhi's best fine-dining restaurant since 2018." },
  { icon: HeartIcon,    title: 'Made with Love',    desc: 'Every dish is plated with intention and served with warmth.' },
];
</script>
