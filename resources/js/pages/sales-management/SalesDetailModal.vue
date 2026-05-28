<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 fade-in">
    <div class="border rounded-2xl shadow-modal w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col slide-up" style="background:var(--card);border-color:var(--border)">
      <!-- Header -->
      <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color:var(--border)">
        <div>
          <div class="flex items-center gap-2">
            <h3 class="font-bold" style="color:var(--foreground)">{{ order.billNo }}</h3>
            <StatusBadge :status="order.status" />
          </div>
          <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">{{ order.salesCode }} · {{ order.date }} at {{ order.time }}</p>
        </div>
        <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><XIcon :size="16" /></button>
      </div>

      <div class="flex-1 overflow-y-auto scrollbar-thin p-5 space-y-5">
        <!-- Meta grid -->
        <div class="grid grid-cols-2 gap-3">
          <div v-for="item in metaItems" :key="item.label" class="rounded-xl p-3" style="background:color-mix(in srgb, var(--muted) 50%, transparent)">
            <div class="flex items-center gap-1.5 mb-1" style="color:var(--muted-foreground)">
              <component :is="item.icon" :size="14" />
              <span class="text-xs font-medium uppercase tracking-wide">{{ item.label }}</span>
            </div>
            <p class="text-sm font-semibold" style="color:var(--foreground)">{{ item.value }}</p>
          </div>
        </div>

        <!-- Items -->
        <div>
          <p class="text-xs font-bold uppercase tracking-wide mb-3" style="color:var(--muted-foreground)">Order Items</p>
          <div class="border rounded-xl overflow-hidden" style="border-color:var(--border)">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b" style="background:color-mix(in srgb, var(--muted) 50%, transparent);border-color:var(--border)">
                  <th class="text-left px-4 py-2.5 text-xs font-semibold" style="color:var(--muted-foreground)">Item</th>
                  <th class="text-center px-3 py-2.5 text-xs font-semibold" style="color:var(--muted-foreground)">Qty</th>
                  <th class="text-right px-4 py-2.5 text-xs font-semibold" style="color:var(--muted-foreground)">Rate</th>
                  <th class="text-right px-4 py-2.5 text-xs font-semibold" style="color:var(--muted-foreground)">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in order.items" :key="item.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors" style="border-color:var(--border)">
                  <td class="px-4 py-2.5">
                    <p class="font-medium" style="color:var(--foreground)">{{ item.name }}</p>
                    <p class="text-xs" style="color:var(--muted-foreground)">{{ item.category }}</p>
                  </td>
                  <td class="px-3 py-2.5 text-center font-semibold" style="color:var(--foreground)">{{ item.quantity }}</td>
                  <td class="px-4 py-2.5 text-right tabular-nums" style="color:var(--foreground)">${{ item.price.toFixed(2) }}</td>
                  <td class="px-4 py-2.5 text-right font-semibold tabular-nums" style="color:var(--foreground)">${{ item.total.toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Payment breakdown -->
        <div class="rounded-xl p-4 space-y-2" style="background:color-mix(in srgb, var(--muted) 30%, transparent)">
          <p class="text-xs font-bold uppercase tracking-wide mb-3" style="color:var(--muted-foreground)">Payment Breakdown</p>
          <div v-for="row in breakdown" :key="row.label" :class="['flex justify-between items-center', row.highlight ? 'pt-2 mt-1 border-t' : '']" :style="row.highlight ? 'border-color:var(--border)' : ''">
            <span :class="['text-sm', row.highlight ? 'font-bold' : '']" :style="row.highlight ? 'color:var(--foreground)' : 'color:var(--muted-foreground)'">{{ row.label }}</span>
            <span :class="['text-sm tabular-nums font-semibold', row.highlight ? 'text-base' : '']" :style="row.highlight ? 'color:var(--primary)' : row.green ? 'color:var(--success)' : row.red ? 'color:var(--danger)' : 'color:var(--foreground)'">{{ row.value }}</span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-5 pb-5 pt-3 flex gap-2 border-t flex-shrink-0" style="border-color:var(--border)">
        <button @click="$emit('close')" class="flex-1 px-4 py-2.5 rounded-lg border text-sm font-medium hover:bg-muted transition-colors" style="border-color:var(--border);color:var(--foreground)">Close</button>
        <button class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border text-sm font-medium hover:bg-muted transition-colors" style="border-color:var(--border);color:var(--foreground)"><DownloadIcon :size="14" /> Invoice</button>
        <button class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg gradient-primary text-white text-sm font-semibold hover:opacity-90 transition-all active:scale-[0.98]"><PrinterIcon :size="14" /> Print</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { X as XIcon, Printer as PrinterIcon, Download as DownloadIcon, User as UserIcon, MapPin as MapPinIcon, Clock as ClockIcon, Tag as TagIcon } from '@lucide/vue';
import StatusBadge from '@/components/ui/StatusBadge.vue';

const props = defineProps({ order: Object });
defineEmits(['close']);

const metaItems = computed(() => [
  { icon: UserIcon, label: 'Customer', value: props.order.customer },
  { icon: MapPinIcon, label: 'Table', value: props.order.table },
  { icon: TagIcon, label: 'Bill Type', value: props.order.billType },
  { icon: ClockIcon, label: 'Waiter', value: props.order.waiter },
]);

const breakdown = computed(() => [
  { label: 'Subtotal', value: `$${props.order.subtotal.toFixed(2)}` },
  { label: 'Tax', value: `$${props.order.tax.toFixed(2)}` },
  { label: 'Discount', value: props.order.discount > 0 ? `-$${props.order.discount.toFixed(2)}` : '—', green: props.order.discount > 0 },
  { label: 'Grand Total', value: `$${props.order.total.toFixed(2)}`, highlight: true },
  { label: 'Paid', value: `$${props.order.paid.toFixed(2)}`, green: true },
  { label: 'Balance Due', value: props.order.balance > 0 ? `$${props.order.balance.toFixed(2)}` : '—', red: props.order.balance > 0 },
]);
</script>
