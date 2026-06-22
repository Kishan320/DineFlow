<template>
  <footer class="border-t pt-16 pb-10 px-4 sm:px-6" style="border-color:var(--lp-border)">
    <div class="max-w-7xl mx-auto">

      <div class="flex flex-col lg:flex-row gap-10 lg:gap-0 justify-between items-start mb-12">

        <!-- Logo + Tagline + Social -->
        <div class="flex flex-col gap-4 max-w-xs">
          <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl lp-gradient-bg-gold flex items-center justify-center shrink-0">
              <span class="text-white font-bold text-base">D</span>
            </div>
            <span class="lp-font-disp text-xl font-semibold tracking-tight" style="color:var(--lp-fg)">DineFlow</span>
          </div>
          <p class="text-sm leading-relaxed" style="color:var(--lp-muted-fg)">
            Where every meal becomes a cherished memory. Fine dining redefined in the heart of New Delhi.
          </p>
          <div class="flex items-center gap-3 mt-1">
            <a v-for="s in socials" :key="s.label" :href="s.href" :aria-label="s.label"
              class="w-9 h-9 rounded-full border flex items-center justify-center transition-all duration-200"
              style="border-color:var(--lp-border);color:var(--lp-muted-fg)"
            >
              <component :is="s.icon" :size="16" />
            </a>
          </div>
        </div>

        <!-- Link Groups -->
        <div class="flex flex-wrap gap-x-12 gap-y-6">
          <div class="flex flex-col gap-3">
            <span class="text-xs font-bold uppercase tracking-widest" style="color:rgba(139,115,85,0.6)">Explore</span>
            <button v-for="href in ['#about','#menu','#gallery','#chefs']" :key="href" @click="scrollTo(href)"
              class="text-sm font-medium text-left transition-colors" style="color:var(--lp-muted-fg)">
              {{ capitalize(href) }}
            </button>
          </div>
          <div class="flex flex-col gap-3">
            <span class="text-xs font-bold uppercase tracking-widest" style="color:rgba(139,115,85,0.6)">Visit</span>
            <button v-for="href in ['#offers','#reservation','#contact']" :key="href" @click="scrollTo(href)"
              class="text-sm font-medium text-left transition-colors" style="color:var(--lp-muted-fg)">
              {{ capitalize(href) }}
            </button>
          </div>
          <div class="flex flex-col gap-3">
            <span class="text-xs font-bold uppercase tracking-widest" style="color:rgba(139,115,85,0.6)">Hours</span>
            <span class="text-sm" style="color:var(--lp-muted-fg)">Mon–Fri: 12pm–11pm</span>
            <span class="text-sm" style="color:var(--lp-muted-fg)">Sat–Sun: 11am–12am</span>
            <span class="text-sm font-semibold" style="color:var(--lp-primary)">Open Today</span>
          </div>
        </div>
      </div>

      <div class="lp-gold-divider mb-6" />

      <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
        <p class="text-sm" style="color:var(--lp-muted-fg)">© {{ year }} DineFlow. Crafted with care in New Delhi.</p>
        <div class="flex items-center gap-6 text-xs font-medium" style="color:var(--lp-muted-fg)">
          <a href="#" class="transition-colors hover:text-[var(--lp-primary)]">Privacy</a>
          <a href="#" class="transition-colors hover:text-[var(--lp-primary)]">Terms</a>
          <a href="#" class="transition-colors hover:text-[var(--lp-primary)]">Sitemap</a>
        </div>
      </div>

    </div>
  </footer>
</template>

<script setup>
import { Globe as GlobeIcon, Instagram as InstagramIcon, Facebook as FacebookIcon } from 'lucide-vue-next';

const year = new Date().getFullYear();

const socials = [
  { label: 'Website',   icon: GlobeIcon,     href: '#' },
  { label: 'Instagram', icon: InstagramIcon, href: '#' },
  { label: 'Facebook',  icon: FacebookIcon,  href: '#' },
];

function scrollTo(href) {
  document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
}

function capitalize(href) {
  const s = href.replace('#', '');
  return s.charAt(0).toUpperCase() + s.slice(1);
}
</script>
