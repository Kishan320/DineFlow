<template>
  <section id="menu" ref="sectionRef" class="py-24 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Full Menu</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Popular&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Selections.</span>
        </h2>
      </div>

      <!-- Tabs -->
      <div class="lp-reveal flex flex-wrap justify-center gap-2 mb-10">
        <button
          v-for="tab in tabs"
          :key="tab"
          @click="activeTab = tab"
          :class="['px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300', activeTab === tab ? 'lp-gradient-bg-gold text-white shadow-lg' : 'border font-semibold hover:text-[var(--lp-primary)]']"
          :style="activeTab !== tab ? { borderColor: 'var(--lp-border)', color: 'var(--lp-muted-fg)', background: 'var(--lp-card)' } : {}"
        >
          {{ tab }}
        </button>
      </div>

      <!-- Menu Items -->
      <div class="space-y-3 lp-reveal">
        <div
          v-for="(item, i) in menuData[activeTab]"
          :key="item.name"
          class="lp-luxury-card p-5 flex items-center justify-between gap-4 group"
          :style="{ transitionDelay: `${i * 60}ms` }"
        >
          <div class="flex items-start gap-4 flex-1 min-w-0">
            <!-- Veg/Non-Veg Indicator -->
            <div :class="['w-5 h-5 rounded border-2 flex items-center justify-center shrink-0 mt-0.5', item.isVeg ? 'border-green-600' : 'border-red-600']">
              <div :class="['w-2.5 h-2.5 rounded-full', item.isVeg ? 'bg-green-600' : 'bg-red-600']" />
            </div>
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h4 class="font-semibold" style="color:var(--lp-card-fg)">{{ item.name }}</h4>
                <span v-if="item.isSignature" class="text-[10px] font-bold px-2 py-0.5 rounded-full border" style="background:rgba(200,150,90,0.1);color:var(--lp-primary);border-color:rgba(200,150,90,0.2)">
                  Signature
                </span>
              </div>
              <p class="text-sm mt-0.5 leading-relaxed" style="color:var(--lp-muted-fg)">{{ item.description }}</p>
            </div>
          </div>

          <div class="flex items-center gap-4 shrink-0">
            <span class="font-bold text-lg" style="color:var(--lp-primary)">₹{{ item.price }}</span>
            <button class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 group-hover:scale-105" style="background:rgba(200,150,90,0.1);color:var(--lp-primary)" @mouseenter="e => e.target.style.background='var(--lp-primary)'" @mouseleave="e => e.target.style.background='rgba(200,150,90,0.1)'">
              <PlusIcon :size="18" />
            </button>
          </div>
        </div>
      </div>

      <div class="text-center mt-10 lp-reveal">
        <p class="text-sm mb-4" style="color:var(--lp-muted-fg)">Full menu available upon reservation. Seasonal items may vary.</p>
        <button @click="scrollTo('#reservation')" class="lp-btn-primary px-8 py-3 text-sm gap-2">
          <CalendarIcon :size="16" color="white" />
          Reserve Your Table
        </button>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { Plus as PlusIcon, Calendar as CalendarIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';

const sectionRef = ref(null);
useReveal(sectionRef);

const tabs = ['Starters', 'Mains', 'Breads', 'Desserts', 'Drinks'];
const activeTab = ref('Starters');

function scrollTo(href) {
  document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
}

const menuData = {
  Starters: [
    { name: 'Gilafi Seekh Kebab',  description: 'Minced lamb with green chillies, coriander, wrapped in bell pepper',     price: 680,  isVeg: false, isSignature: true },
    { name: 'Dahi Ke Sholay',      description: 'Crispy yogurt fritters with tamarind glaze and mint foam',                price: 480,  isVeg: true },
    { name: 'Prawn Koliwada',      description: 'Coastal-style batter-fried prawns with kokum aioli',                      price: 820,  isVeg: false },
    { name: 'Hara Bhara Galawat',  description: 'Spinach and pea patties with cashew cream',                               price: 420,  isVeg: true,  isSignature: true },
    { name: 'Tandoori Broccoli',   description: 'Char-grilled florets in house masala, served with chutney trio',          price: 380,  isVeg: true },
  ],
  Mains: [
    { name: 'Butter Chicken Supreme', description: 'Free-range chicken in silken tomato-fenugreek sauce',                 price: 1280, isVeg: false, isSignature: true },
    { name: 'Rogan Josh Classique',   description: 'Kashmiri lamb braised with 14 whole spices',                          price: 1480, isVeg: false },
    { name: 'Dal Makhani Royale',     description: 'Slow-cooked black lentils in tomato-cream reduction',                 price: 680,  isVeg: true,  isSignature: true },
    { name: 'Malai Kofta Blanc',      description: 'Cottage cheese dumplings in white cream gravy',                       price: 720,  isVeg: true },
    { name: 'Amritsari Machhi Curry', description: 'River sole in a spiced mustard-onion gravy',                          price: 1180, isVeg: false },
  ],
  Breads: [
    { name: 'Butter Naan',       description: 'Classic leavened flatbread with cultured butter',              price: 120, isVeg: true },
    { name: 'Lachha Paratha',    description: 'Multi-layered whole wheat flatbread, stone-fired',              price: 150, isVeg: true },
    { name: 'Missi Roti',        description: 'Spiced chickpea flour bread with carom seeds',                 price: 130, isVeg: true, isSignature: true },
    { name: 'Kulcha Stuffed',    description: 'Amritsari-style stuffed bread — potato or paneer',             price: 180, isVeg: true },
  ],
  Desserts: [
    { name: 'Gulab Jamun Soufflé', description: 'Rose-saffron soufflé with cardamom ice cream',               price: 520, isVeg: true, isSignature: true },
    { name: 'Shahi Tukda Redux',   description: 'Saffron bread pudding with rabri and pistachio crumble',     price: 480, isVeg: true },
    { name: 'Kulfi Falooda Moderne', description: 'Deconstructed falooda with pistachio kulfi sphere',        price: 440, isVeg: true },
    { name: 'Gajar Halwa Tart',    description: 'Seasonal carrot halwa in a flaky pastry shell',             price: 390, isVeg: true },
  ],
  Drinks: [
    { name: 'Mango Saffron Lassi', description: 'Alphonso mango with saffron yogurt and rose foam',           price: 380, isVeg: true, isSignature: true },
    { name: 'Thandai Old Fashioned', description: 'House thandai blend with rose water and bitters',          price: 420, isVeg: true },
    { name: 'Spiced Nimbu Pani',   description: 'Fresh lime with black salt, cumin, and mint',               price: 220, isVeg: true },
    { name: 'Aam Panna Fizz',      description: 'Raw mango shrub with sparkling water and chaat masala',      price: 280, isVeg: true },
  ],
};
</script>
