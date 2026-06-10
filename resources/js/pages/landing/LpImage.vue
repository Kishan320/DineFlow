<template>
  <img
    :src="currentSrc"
    :alt="alt"
    :width="width"
    :height="height"
    :loading="priority ? 'eager' : 'lazy'"
    :class="className"
    :style="fill ? 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover' : ''"
    @error="onError"
    v-bind="$attrs"
  />
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  src:       { type: String, required: true },
  alt:       { type: String, default: '' },
  width:     { type: [Number, String], default: undefined },
  height:    { type: [Number, String], default: undefined },
  class:     { type: String, default: '' },
  fill:      { type: Boolean, default: false },
  priority:  { type: Boolean, default: false },
  fallback:  { type: String, default: '' },
});

const currentSrc = ref(props.src);
const className  = ref(props.class);

function onError() {
  if (props.fallback && currentSrc.value !== props.fallback) {
    currentSrc.value = props.fallback;
  }
}
</script>
