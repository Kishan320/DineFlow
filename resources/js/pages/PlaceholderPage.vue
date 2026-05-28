<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-bold" style="color:var(--foreground)">{{ title }}</h1>
      <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">{{ breadcrumb }}</p>
    </div>
    <div class="border rounded-xl p-12 text-center" style="background:var(--card);border-color:var(--border)">
      <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:var(--muted)">
        <ConstructionIcon :size="28" style="color:var(--muted-foreground)" />
      </div>
      <h2 class="text-lg font-semibold mb-2" style="color:var(--foreground)">Coming Soon</h2>
      <p class="text-sm" style="color:var(--muted-foreground)">This section is under development.</p>
    </div>
  </div>
</template>

<script setup>
import { Construction as ConstructionIcon } from '@lucide/vue';
defineProps({ title: String, breadcrumb: String });
</script>
