<template>
  <Toaster
    position="bottom-right"
    :expand="true"
    :rich-colors="true"
    :close-button="true"
    :duration="5000"
  />
</template>

<script setup>
import { Toaster } from 'vue-sonner';
</script>
