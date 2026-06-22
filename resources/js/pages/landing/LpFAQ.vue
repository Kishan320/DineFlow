<template>
  <section id="faq" ref="sectionRef" class="py-24 px-4 sm:px-6" style="background:rgba(240,234,224,0.3)">
    <div class="max-w-3xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Guest Questions</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Frequently&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Asked.</span>
        </h2>
      </div>

      <div class="space-y-3 lp-reveal">
        <div v-for="(faq, i) in faqs" :key="i" class="lp-luxury-card overflow-hidden">
          <button
            @click="openIndex = openIndex === i ? null : i"
            class="w-full flex items-center justify-between p-6 text-left gap-4"
            :aria-expanded="openIndex === i"
          >
            <span class="font-semibold text-base leading-snug pr-4" style="color:var(--lp-card-fg)">{{ faq.q }}</span>
            <div
              :class="['w-8 h-8 rounded-full shrink-0 flex items-center justify-center transition-all duration-300', openIndex === i ? 'rotate-180' : '']"
              :style="openIndex === i ? { background: 'var(--lp-primary)', color: '#fff' } : { background: 'var(--lp-muted)', color: 'var(--lp-muted-fg)' }"
            >
              <ChevronDownIcon :size="18" />
            </div>
          </button>

          <div :class="['lp-accordion-content', openIndex === i ? 'lp-open' : '']">
            <div class="px-6 pb-6">
              <div class="lp-gold-divider mb-4" />
              <p class="leading-relaxed" style="color:var(--lp-muted-fg)">{{ faq.a }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { ChevronDown as ChevronDownIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';

const sectionRef = ref(null);
useReveal(sectionRef);

const openIndex = ref(0);

const faqs = [
  { q: 'Do you accommodate dietary restrictions and allergies?',       a: 'Absolutely. We offer Jain, vegan, gluten-free, and nut-free preparations for all our dishes. Please inform us at the time of reservation and our chefs will ensure your meal is prepared with care.' },
  { q: 'Is there a dress code at DineFlow?',                           a: 'We maintain a smart-casual to formal dress code. We request guests to refrain from sportswear and flip-flops. For Chef\'s Table experiences, formal attire is preferred.' },
  { q: 'Can I host a private event or corporate dinner?',              a: 'Yes. Our private dining room seats up to 20 guests and our full restaurant can be booked exclusively for groups of 80+. Contact us at events@dineflow.in for customized packages.' },
  { q: 'How far in advance should I make a reservation?',             a: 'We recommend booking at least 3 days in advance for weekday dinners and 7–10 days for weekends. For Chef\'s Table, we suggest booking 2–3 weeks ahead as spots are extremely limited.' },
  { q: 'Do you offer valet parking?',                                  a: 'Yes, complimentary valet parking is available for all dinner reservations. For lunch, paid parking is available in the adjacent Connaught Place underground complex.' },
  { q: 'Can I bring my own cake for a celebration?',                   a: 'You are welcome to bring an external cake for a corkage fee of ₹500. Alternatively, our pastry team can create a custom celebration cake with 48 hours notice.' },
];
</script>
