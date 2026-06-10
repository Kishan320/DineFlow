<template>
  <section id="newsletter" ref="sectionRef" class="py-20 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto text-center lp-reveal">
      <div class="lp-luxury-card p-10 md:p-14 relative overflow-hidden lp-glow-gold">
        <!-- Decorative blobs -->
        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full pointer-events-none" style="background:rgba(200,150,90,0.1);filter:blur(48px)" />
        <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full pointer-events-none" style="background:rgba(232,184,122,0.1);filter:blur(48px)" />

        <div class="relative z-10">
          <div class="w-14 h-14 mx-auto rounded-2xl lp-gradient-bg-gold flex items-center justify-center mb-6">
            <MailIcon :size="26" color="white" />
          </div>
          <h2 class="text-3xl md:text-4xl lp-font-disp font-semibold mb-3" style="color:var(--lp-fg)">
            Stay at the&nbsp;<span class="lp-gradient-gold lp-font-disp italic">Table.</span>
          </h2>
          <p class="mb-8 leading-relaxed max-w-md mx-auto" style="color:var(--lp-muted-fg)">
            Receive seasonal menus, exclusive offers, and chef's notes before anyone else. No spam — only the good stuff.
          </p>

          <!-- Success -->
          <div v-if="status === 'success'" class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 rounded-full lp-gradient-bg-gold flex items-center justify-center">
              <CheckIcon :size="22" color="white" />
            </div>
            <p class="font-semibold" style="color:var(--lp-fg)">You're on the list!</p>
            <p class="text-sm" style="color:var(--lp-muted-fg)">Watch your inbox for something special from Chef Arjun.</p>
          </div>

          <!-- Form -->
          <form v-else @submit.prevent="handleSubmit" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
            <div class="flex-1">
              <input
                v-model="email"
                type="email"
                placeholder="your@email.com"
                class="lp-input"
                @input="error = ''"
              />
              <p v-if="error" class="text-xs text-red-500 mt-1 text-left">{{ error }}</p>
            </div>
            <button
              type="submit"
              :disabled="status === 'loading'"
              class="lp-btn-primary px-7 py-3.5 text-sm shrink-0 justify-center gap-2"
              style="border-radius:var(--lp-radius)"
            >
              <span v-if="status === 'loading'" class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin" />
              <template v-else>
                Subscribe
                <ArrowRightIcon :size="16" color="white" />
              </template>
            </button>
          </form>

          <p class="text-xs mt-4" style="color:var(--lp-muted-fg)">Unsubscribe anytime. We respect your inbox.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { Mail as MailIcon, Check as CheckIcon, ArrowRight as ArrowRightIcon } from 'lucide-vue-next';
import { useReveal } from '@/composables/useReveal.js';

const sectionRef = ref(null);
useReveal(sectionRef, { threshold: 0.1 });

const email  = ref('');
const error  = ref('');
const status = ref('idle'); // idle | loading | success

async function handleSubmit() {
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    error.value = 'Please enter a valid email address.';
    return;
  }
  error.value = '';
  status.value = 'loading';
  await new Promise(r => setTimeout(r, 1200));
  status.value = 'success';
}
</script>
