<template>
  <div class="flex items-center justify-center flex-shrink-0" :style="{ width: size + 'px', height: size + 'px' }">
    <div class="w-full h-full rounded-xl gradient-primary flex items-center justify-center">
      <span class="text-white font-bold" :style="{ fontSize: size * 0.4 + 'px' }">R</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  size: { type: Number, default: 32 },
});
</script>
