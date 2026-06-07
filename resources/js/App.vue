<template>
  <RouterView />
</template>

<script setup>
import { onMounted } from 'vue';
import { RouterView } from 'vue-router';
import { useAuthStore } from '@/stores/authStore.js';

const authStore = useAuthStore();

onMounted(() => {
    if (authStore.isAuthenticated) {
        authStore.fetchMe();
    }
});
</script>
