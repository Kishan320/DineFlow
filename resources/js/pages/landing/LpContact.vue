<template>
  <section id="contact" ref="sectionRef" class="py-24 px-4 sm:px-6" style="background:rgba(240,234,224,0.3)">
    <div class="max-w-7xl mx-auto">

      <div class="text-center mb-12 lp-reveal">
        <p class="text-xs font-bold uppercase tracking-[0.3em] mb-3" style="color:var(--lp-primary)">Find Us</p>
        <h2 class="lp-text-section lp-font-disp font-medium tracking-tight" style="color:var(--lp-fg)">
          Come&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Visit.</span>
        </h2>
      </div>

      <div class="grid lg:grid-cols-5 gap-8 items-start">

        <!-- Contact Info -->
        <div class="lg:col-span-2 space-y-4 lp-reveal">
          <div v-for="item in contactDetails" :key="item.label" class="lp-luxury-card p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(200,150,90,0.1)">
              <component :is="item.icon" :size="20" style="color:var(--lp-primary)" />
            </div>
            <div>
              <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color:var(--lp-muted-fg)">{{ item.label }}</p>
              <a v-if="item.link" :href="item.link" :target="item.link.startsWith('http') ? '_blank' : undefined" rel="noopener noreferrer"
                class="text-sm font-medium leading-relaxed transition-colors" style="color:var(--lp-fg)">
                {{ item.value }}
              </a>
              <p v-else class="text-sm font-medium leading-relaxed" style="color:var(--lp-fg)">{{ item.value }}</p>
            </div>
          </div>

          <!-- Social -->
          <div class="lp-luxury-card p-5">
            <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color:var(--lp-muted-fg)">Follow Us</p>
            <div class="flex gap-3">
              <a v-for="s in socials" :key="s.label" :href="s.href" :aria-label="s.label"
                class="w-10 h-10 rounded-full border flex items-center justify-center transition-all duration-200"
                style="border-color:var(--lp-border);color:var(--lp-muted-fg)"
              >
                <component :is="s.icon" :size="18" />
              </a>
            </div>
          </div>
        </div>

        <!-- Map -->
        <div class="lg:col-span-3 lp-reveal lp-stagger-1">
          <div class="lp-luxury-card overflow-hidden rounded-2xl" style="height:420px">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.994!2d77.2195!3d28.6315!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfd37b741d057%3A0xcdee88e47393c3f1!2sConnaught+Place%2C+New+Delhi!5e0!3m2!1sen!2sin!4v1686000000000!5m2!1sen!2sin"
              width="100%" height="100%"
              style="border:0;filter:saturate(0.8) contrast(1.05)"
              allowfullscreen loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="LuxeDine Location — Connaught Place, New Delhi"
            />
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { MapPin as MapPinIcon, Phone as PhoneIcon, Mail as MailIcon, Clock as ClockIcon, Globe as GlobeIcon, Instagram as InstagramIcon, Facebook as FacebookIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';

const sectionRef = ref(null);
useReveal(sectionRef);

const contactDetails = [
  { icon: MapPinIcon, label: 'Address',      value: '12, Janpath, Connaught Place, New Delhi — 110001', link: 'https://maps.google.com' },
  { icon: PhoneIcon,  label: 'Reservations', value: '+91 11 4567 8900',   link: 'tel:+911145678900' },
  { icon: MailIcon,   label: 'Email',        value: 'hello@luxedine.in',  link: 'mailto:hello@luxedine.in' },
  { icon: ClockIcon,  label: 'Hours',        value: 'Lunch: 12–3pm · Dinner: 7–11pm · Daily', link: null },
];

const socials = [
  { label: 'Instagram', icon: InstagramIcon, href: '#' },
  { label: 'Facebook',  icon: FacebookIcon,  href: '#' },
  { label: 'Website',   icon: GlobeIcon,     href: '#' },
];
</script>
