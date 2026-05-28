<template>
  <span :class="['inline-flex items-center gap-1.5 font-semibold rounded-full', sizeClass, config.className]">
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70 flex-shrink-0" />
    {{ status }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: { type: String, required: true },
  size: { type: String, default: 'sm' },
});

const statusConfig = {
  Paid: { className: 'status-paid' },
  Pending: { className: 'status-pending' },
  Draft: { className: 'status-draft' },
  Cancelled: { className: 'status-cancelled' },
  Active: { className: 'status-active' },
  Available: { className: 'status-paid' },
  Occupied: { className: 'status-pending' },
  Reserved: { className: 'status-active' },
  Busy: { className: 'status-pending' },
  'Off Duty': { className: 'status-draft' },
};

const config = computed(() => statusConfig[props.status] || { className: 'status-draft' });
const sizeClass = computed(() => props.size === 'md' ? 'text-sm px-3 py-1' : 'text-xs px-2.5 py-0.5');
</script>
