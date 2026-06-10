<template>
  <div :class="['landing-page min-h-screen overflow-x-hidden', uiStore.darkMode ? 'dark' : '']">
    <RouterView />
  </div>
</template>

<script setup>
import { useUIStore } from '@/stores/uiStore.js';
const uiStore = useUIStore();
</script>
