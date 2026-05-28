<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 fade-in">
    <div class="border rounded-2xl shadow-modal w-full max-w-xs text-center p-8 slide-up" style="background:var(--card);border-color:var(--border)">
      <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:color-mix(in srgb, var(--success) 10%, transparent)">
        <CheckCircleIcon :size="32" style="color:var(--success)" />
      </div>
      <h3 class="text-lg font-bold mb-1" style="color:var(--foreground)">Order Placed!</h3>
      <p class="text-sm mb-1" style="color:var(--muted-foreground)">{{ billNo }}</p>
      <p class="text-3xl font-bold tabular-nums mb-6" style="color:var(--primary)">${{ total.toFixed(2) }}</p>
      <div class="space-y-2">
        <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-medium hover:bg-muted transition-colors" style="border-color:var(--border);color:var(--foreground)">
          <PrinterIcon :size="15" /> Print Receipt
        </button>
        <button @click="$emit('close')" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl gradient-primary text-white text-sm font-semibold hover:opacity-90 transition-all active:scale-[0.98]">
          New Order
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { CheckCircle as CheckCircleIcon, Printer as PrinterIcon } from '@lucide/vue';

defineProps({ billNo: String, total: Number });
defineEmits(['close']);
</script>
