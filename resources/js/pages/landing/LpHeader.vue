<template>
  <nav
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-500',
      scrolled ? 'lp-glass-nav py-3 shadow-sm' : 'py-5 bg-transparent'
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between">
      <!-- Logo -->
      <button @click="scrollTop" class="flex items-center gap-2 group" aria-label="DineFlow Home">
        <div class="w-9 h-9 rounded-xl lp-gradient-bg-gold flex items-center justify-center shrink-0">
          <span class="text-white font-bold text-base">D</span>
        </div>
        <span class="lp-font-disp text-xl font-semibold tracking-tight group-hover:text-[var(--lp-primary)] transition-colors"
              :style="{ color: scrolled ? 'var(--lp-fg)' : '#fff' }">
          DineFlow
        </span>
      </button>

      <!-- Desktop Nav -->
      <div class="hidden lg:flex items-center gap-8">
        <button
          v-for="link in navLinks"
          :key="link.href"
          @click="scrollTo(link.href)"
          class="text-sm font-semibold transition-colors duration-200 tracking-wide hover:text-[var(--lp-primary)]"
          :style="{ color: scrolled ? 'var(--lp-muted-fg)' : 'rgba(255,255,255,0.8)' }"
        >
          {{ link.label }}
        </button>
      </div>

      <!-- Right Controls -->
      <div class="flex items-center gap-3">
        <!-- Theme Toggle -->
        <button
          @click="uiStore.toggleDark()"
          aria-label="Toggle theme"
          class="w-10 h-10 rounded-full flex items-center justify-center border transition-all duration-300 hover:scale-105"
          :style="{ borderColor: 'var(--lp-border)', background: scrolled ? 'var(--lp-muted)' : 'rgba(255,255,255,0.1)' }"
        >
          <component :is="uiStore.darkMode ? SunIcon : MoonIcon" :size="18"
            :color="uiStore.darkMode ? 'var(--lp-accent)' : (scrolled ? 'var(--lp-primary)' : '#fff')"
            :style="{ transition: 'transform 0.5s', transform: uiStore.darkMode ? 'rotate(0deg)' : 'rotate(180deg)' }"
          />
        </button>

        <!-- Dashboard link (authenticated users) -->
        <button
          v-if="authStore.isAuthenticated"
          @click="router.push('/dashboard')"
          class="hidden sm:flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-full border transition-all duration-200"
          :style="{ borderColor: 'var(--lp-border)', color: scrolled ? 'var(--lp-primary)' : '#fff', background: scrolled ? 'var(--lp-muted)' : 'rgba(255,255,255,0.1)' }"
        >
          <DashboardIcon :size="15" />
          Dashboard
        </button>
         <button
          v-else
          @click="router.push('/login')"
          class="hidden sm:flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-full border transition-all duration-200"
          :style="{ borderColor: 'var(--lp-border)', color: scrolled ? 'var(--lp-primary)' : '#fff', background: scrolled ? 'var(--lp-muted)' : 'rgba(255,255,255,0.1)' }"
        >
          <LogIn :size="15" />
          Login
        </button>
        <!-- Book Table CTA -->
        <button
          @click="scrollTo('#reservation')"
          class="hidden sm:flex lp-btn-primary px-5 py-2.5 text-sm gap-2"
        >
          <CalendarIcon :size="16" color="white" />
          Book Table
        </button>

        <!-- Hamburger -->
        <button
          @click="mobileOpen = !mobileOpen"
          class="lg:hidden w-10 h-10 flex items-center justify-center rounded-full border"
          :style="{ borderColor: 'var(--lp-border)', background: scrolled ? 'var(--lp-muted)' : 'rgba(255,255,255,0.1)' }"
          aria-label="Toggle menu"
        >
          <component :is="mobileOpen ? XIcon : MenuIcon" :size="20"
            :color="scrolled ? 'var(--lp-fg)' : '#fff'"
          />
        </button>
      </div>
    </div>
  </nav>

  <!-- Mobile Menu Overlay -->
  <div
    class="fixed inset-0 z-40 lg:hidden transition-all duration-500"
    :class="mobileOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
    style="backdrop-filter:blur(20px);background:rgba(15,13,11,0.92)"
  >
    <div class="flex flex-col items-center justify-center h-full gap-8 px-6">
      <button
        v-for="(link, i) in navLinks"
        :key="link.href"
        @click="scrollTo(link.href)"
        class="text-2xl lp-font-disp font-medium text-white hover:text-[var(--lp-accent)] transition-colors duration-200"
        :style="{ transitionDelay: `${i * 60}ms` }"
      >
        {{ link.label }}
      </button>
      <button @click="scrollTo('#reservation')" class="lp-btn-primary px-8 py-3 text-base mt-4 gap-2">
        <CalendarIcon :size="18" color="white" />
        Book a Table
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { Sun as SunIcon, Moon as MoonIcon, Calendar as CalendarIcon, Menu as MenuIcon, X as XIcon, LayoutDashboard as DashboardIcon, LogIn } from 'lucide-vue-next';
import { useUIStore } from '@/stores/uiStore.js';
import { useAuthStore } from '@/stores/authStore.js';

const router    = useRouter();
const authStore = useAuthStore();
const uiStore   = useUIStore();
const scrolled   = ref(false);
const mobileOpen = ref(false);

const navLinks = [
  { label: 'About',   href: '#about' },
  { label: 'Menu',    href: '#menu' },
  { label: 'Offers',  href: '#offers' },
  { label: 'Gallery', href: '#gallery' },
  { label: 'Chefs',   href: '#chefs' },
  { label: 'Contact', href: '#contact' },
];

function scrollTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function scrollTo(href) {
  mobileOpen.value = false;
  document.body.style.overflow = '';
  document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
}

function onScroll() {
  scrolled.value = window.scrollY > 60;
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>
