<template>
  <div class="min-h-screen flex items-center justify-center p-6" style="background:var(--background)">
    <div class="text-center max-w-md">
      <div class="w-24 h-24 rounded-full mx-auto mb-6 flex items-center justify-center" style="background:color-mix(in srgb, var(--danger) 10%, transparent)">
        <ShieldOffIcon :size="40" style="color:var(--danger)" />
      </div>
      <h1 class="text-3xl font-bold mb-2" style="color:var(--foreground)">Access Denied</h1>
      <p class="text-sm mb-6" style="color:var(--muted-foreground)">
        You don't have permission to view this page. Contact your administrator if you think this is a mistake.
      </p>
      <div class="flex gap-3 justify-center">
        <button @click="router.back()"
          class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted"
          style="border-color:var(--border);color:var(--foreground)">
          Go Back
        </button>
        <button @click="router.push('/')"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          style="background:var(--primary);color:var(--primary-foreground)">
          Dashboard
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { ShieldOff as ShieldOffIcon } from '@lucide/vue';
const router = useRouter();
</script>
