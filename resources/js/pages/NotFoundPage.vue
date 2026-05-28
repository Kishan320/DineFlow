<template>
  <div class="flex flex-col items-center justify-center min-h-screen text-center p-8" style="background:var(--background)">
    <div class="w-20 h-20 rounded-2xl gradient-primary flex items-center justify-center mb-6">
      <span class="text-white text-3xl font-bold">404</span>
    </div>
    <h1 class="text-2xl font-bold mb-2" style="color:var(--foreground)">Page Not Found</h1>
    <p class="text-sm mb-6" style="color:var(--muted-foreground)">The page you're looking for doesn't exist.</p>
    <RouterLink to="/" class="px-6 py-2.5 rounded-xl gradient-primary text-white font-semibold text-sm hover:opacity-90 transition-all">
      Back to Dashboard
    </RouterLink>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>
