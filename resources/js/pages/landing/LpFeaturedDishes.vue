<template>
  <section id="dishes" ref="sectionRef" class="py-24 px-4 sm:px-6" style="background:rgba(var(--lp-muted-rgb,240,234,224),0.3)">
    <div class="max-w-7xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Curated Selection</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Featured&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Dishes.</span>
        </h2>
      </div>

      <!-- Filter Tabs -->
      <div class="lp-reveal flex flex-wrap justify-center gap-2 mb-12">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="activeCategory = cat"
          :class="['px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300', activeCategory === cat ? 'lp-btn-primary text-white' : 'border text-sm font-semibold hover:text-[var(--lp-primary)]']"
          :style="activeCategory !== cat ? { borderColor: 'var(--lp-border)', color: 'var(--lp-muted-fg)', background: 'var(--lp-card)' } : {}"
        >
          {{ cat }}
        </button>
      </div>

      <!-- Dish Cards -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(dish, i) in filtered"
          :key="dish.id"
          class="lp-reveal lp-luxury-card overflow-hidden group"
          :style="{ transitionDelay: `${i * 80}ms` }"
        >
          <!-- Image -->
          <div class="relative aspect-[4/3] overflow-hidden">
            <LpImage
              :src="dish.image"
              :alt="dish.alt"
              :fill="true"
              class="object-cover group-hover:scale-105 transition-transform duration-700"
            />
            <span v-if="dish.badge" class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-bold text-white shadow-md" style="background:var(--lp-primary)">
              {{ dish.badge }}
            </span>
            <button
              @click="toggleFav(dish.id)"
              :aria-label="favorites.includes(dish.id) ? 'Remove from favorites' : 'Add to favorites'"
              class="absolute top-3 right-3 w-9 h-9 rounded-full lp-glass-card flex items-center justify-center transition-transform duration-200 hover:scale-110"
            >
              <HeartIcon :size="18" :color="favorites.includes(dish.id) ? '#ef4444' : 'var(--lp-fg)'" :fill="favorites.includes(dish.id) ? '#ef4444' : 'none'" />
            </button>
          </div>

          <!-- Content -->
          <div class="p-5">
            <div class="flex items-start justify-between gap-2 mb-2">
              <h3 class="lp-font-disp font-semibold text-lg leading-tight" style="color:var(--lp-card-fg)">{{ dish.name }}</h3>
              <span class="font-bold text-lg shrink-0" style="color:var(--lp-primary)">₹{{ dish.price }}</span>
            </div>
            <p class="text-sm leading-relaxed mb-4" style="color:var(--lp-muted-fg)">{{ dish.description }}</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1">
                <StarIcon :size="14" fill="#f59e0b" color="#f59e0b" />
                <span class="text-sm font-semibold" style="color:var(--lp-fg)">{{ dish.rating }}</span>
              </div>
              <span :class="['text-xs font-semibold px-2.5 py-1 rounded-full', categoryBadgeClass(dish.category)]">
                {{ dish.category }}
              </span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Heart as HeartIcon, Star as StarIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';
import LpImage from './LpImage.vue';

const sectionRef = ref(null);
useReveal(sectionRef);

const categories = ['All', 'Veg', 'Non-Veg', 'Desserts', 'Drinks'];
const activeCategory = ref('All');
const favorites = ref([]);

const dishes = [
  { id: 1, name: 'Dal Makhani Royale',   description: 'Slow-cooked black lentils in a velvety tomato-cream reduction, finished with truffle butter.',          price: 680,  category: 'Veg',     image: 'https://ik.imagekit.io/dineflowimages/landing/image-9.png',  alt: 'Creamy dark lentil curry',          badge: "Chef's Pick", rating: 4.9 },
  { id: 2, name: 'Rogan Josh Classique', description: 'Kashmiri lamb slow-braised with 14 whole spices in a rich, aromatic gravy.',                              price: 1480, category: 'Non-Veg', image: 'https://ik.imagekit.io/dineflowimages/landing/image-10.png',  alt: 'Rich red lamb curry',               badge: 'Bestseller',  rating: 4.8 },
  { id: 3, name: 'Gulab Jamun Soufflé',  description: 'A deconstructed Indian classic — warm rose-saffron soufflé with cardamom ice cream.',                     price: 520,  category: 'Desserts', image: 'https://ik.imagekit.io/dineflowimages/landing/image-11.jpg',                           alt: 'Golden dessert soufflé',             badge: 'New',         rating: 4.7 },
  { id: 4, name: 'Paneer Tikka Noir',    description: 'House-smoked cottage cheese in activated charcoal marinade with mango chutney gel.',                       price: 780,  category: 'Veg',     image: 'https://ik.imagekit.io/dineflowimages/landing/image-12.jpg',                           alt: 'Charcoal-marinated paneer skewers', badge: null,          rating: 4.6 },
  { id: 5, name: 'Butter Chicken Supreme', description: 'Free-range chicken in a silken tomato-fenugreek sauce aged for 48 hours.',                              price: 1280, category: 'Non-Veg', image: 'https://ik.imagekit.io/dineflowimages/landing/image-13.png',  alt: 'Vibrant orange butter chicken',     badge: 'Signature',   rating: 4.9 },
  { id: 6, name: 'Mango Saffron Lassi',  description: 'Alphonso mango blended with saffron-infused yogurt and a rose water foam crown.',                          price: 380,  category: 'Drinks',  image: 'https://ik.imagekit.io/dineflowimages/landing/image-14.png',  alt: 'Golden mango lassi',                badge: null,          rating: 4.7 },
];

const filtered = computed(() =>
  activeCategory.value === 'All' ? dishes : dishes.filter(d => d.category === activeCategory.value)
);

function toggleFav(id) {
  favorites.value = favorites.value.includes(id)
    ? favorites.value.filter(f => f !== id)
    : [...favorites.value, id];
}

function categoryBadgeClass(cat) {
  return {
    Veg:     'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    'Non-Veg': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    Desserts: 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
    Drinks:  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
  }[cat] || '';
}
</script>
