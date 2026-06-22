<template>
  <section id="chefs" ref="sectionRef" class="py-24 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Culinary Artists</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Meet the&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Chefs.</span>
        </h2>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="(chef, i) in chefs"
          :key="chef.id"
          class="lp-reveal lp-luxury-card overflow-hidden group"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <!-- Image -->
          <div class="relative aspect-[3/4] overflow-hidden">
            <LpImage :src="chef.image" :alt="chef.alt" :fill="true" class="object-cover group-hover:scale-105 transition-transform duration-700" style="filter:grayscale(20%)" />
            <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%)" />
            <!-- Hover Awards -->
            <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
              <div v-for="award in chef.awards" :key="award" class="flex items-center gap-1.5 mb-1">
                <TrophyIcon :size="12" fill="#f59e0b" color="#f59e0b" class="shrink-0" />
                <span class="text-[10px] text-white/90 font-medium">{{ award }}</span>
              </div>
            </div>
          </div>

          <!-- Info -->
          <div class="p-5 space-y-2">
            <h3 class="lp-font-disp font-semibold text-lg leading-tight" style="color:var(--lp-card-fg)">{{ chef.name }}</h3>
            <p class="text-xs font-bold tracking-wide" style="color:var(--lp-primary)">{{ chef.title }}</p>
            <p class="text-xs leading-relaxed" style="color:var(--lp-muted-fg)">{{ chef.bio }}</p>
            <div class="flex items-center justify-between pt-2">
              <span class="text-xs rounded-full px-3 py-1 font-medium" style="background:var(--lp-muted);color:var(--lp-muted-fg)">{{ chef.specialty }}</span>
              <span class="text-xs font-semibold" style="color:var(--lp-muted-fg)">{{ chef.experience }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { Trophy as TrophyIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';
import LpImage from './LpImage.vue';

const sectionRef = ref(null);
useReveal(sectionRef);

const chefs = [
  { id: 1, name: 'Chef Arjun Malhotra', title: 'Executive Chef & Founder',  specialty: 'North Indian & Fusion',  experience: '24 years', image: 'https://ik.imagekit.io/dineflowimages/landing/image-5.png',  alt: 'Male executive chef in white uniform', awards: ['National Chef Award 2023', 'Michelin Recognition 2022'],  bio: 'Trained at Le Cordon Bleu Paris, Chef Arjun returned to India to create a cuisine that honors tradition while embracing the world.' },
  { id: 2, name: 'Chef Kavitha Iyer',   title: 'Head Chef — South Indian',  specialty: 'Kerala & Tamil Cuisine', experience: '16 years', image: 'https://ik.imagekit.io/dineflowimages/landing/image-6.png',  alt: 'Female chef in white uniform',         awards: ['Best Regional Chef 2022', 'Forbes 30 Under 30'],          bio: "Chef Kavitha brings the coastal kitchens of Kerala to life, sourcing ingredients from her family's spice farm in Wayanad." },
  { id: 3, name: 'Chef Ravi Tiwari',    title: 'Pastry & Desserts Chef',    specialty: 'Fusion Desserts',        experience: '12 years', image: 'https://ik.imagekit.io/dineflowimages/landing/image-7.png',  alt: 'Young male pastry chef decorating',    awards: ['Pastry Chef of the Year 2023'],                           bio: "A graduate of the Culinary Institute of America, Ravi transforms India's sweet traditions into modern edible art." },
  { id: 4, name: 'Chef Sunita Rao',     title: 'Sous Chef',                 specialty: 'Rajasthani & Mughlai',  experience: '10 years', image: 'https://ik.imagekit.io/dineflowimages/landing/image-8.png',  alt: 'Female sous chef in white uniform',    awards: ['Rising Star Chef 2023'],                                  bio: "Sunita's mastery of slow-cooked Rajasthani and Mughlai techniques brings depth and history to every plate she creates." },
];
</script>
