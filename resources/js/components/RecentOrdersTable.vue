<template>
  <div class="border rounded-xl shadow-card overflow-hidden" style="background:var(--card);border-color:var(--border)">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
      <div>
        <h2 class="text-base font-semibold" style="color:var(--foreground)">Recent Orders</h2>
        <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">Last 10 transactions today</p>
      </div>
      <RouterLink to="/sales-management" class="text-xs font-medium hover:underline" style="color:var(--primary)">View all →</RouterLink>
    </div>
    <div class="overflow-x-auto scrollbar-thin">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b" style="border-color:var(--border);background:color-mix(in srgb, var(--muted) 50%, transparent)">
            <th v-for="col in columns" :key="col" class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color:var(--muted-foreground)">{{ col }}</th>
            <th class="px-4 py-3" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(order, idx) in recentOrders.slice(0, 8)"
            :key="order.id"
            :class="['border-b last:border-0 hover:bg-muted/40 transition-colors group', idx % 2 !== 0 ? 'bg-muted/20' : '']"
            style="border-color:var(--border)"
          >
            <td class="px-4 py-3 font-mono text-xs font-medium whitespace-nowrap" style="color:var(--foreground)">{{ order.billNo }}</td>
            <td class="px-4 py-3 text-sm whitespace-nowrap" style="color:var(--foreground)">{{ order.customer }}</td>
            <td class="px-4 py-3 text-sm whitespace-nowrap" style="color:var(--muted-foreground)">{{ order.table }}</td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span :class="['text-xs font-medium px-2 py-0.5 rounded-full', billTypeClass(order.billType)]">{{ order.billType }}</span>
            </td>
            <td class="px-4 py-3 text-sm font-semibold text-right tabular-nums whitespace-nowrap" style="color:var(--foreground)">${{ order.total.toFixed(2) }}</td>
            <td class="px-4 py-3 whitespace-nowrap"><StatusBadge :status="order.status" /></td>
            <td class="px-4 py-3 text-xs whitespace-nowrap" style="color:var(--muted-foreground)">{{ order.time }}</td>
            <td class="px-4 py-3 whitespace-nowrap">
              <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><EyeIcon :size="14" /></button>
                <button class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><PrinterIcon :size="14" /></button>
                <button class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><MoreHorizontalIcon :size="14" /></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router';
import { Eye as EyeIcon, Printer as PrinterIcon, MoreHorizontal as MoreHorizontalIcon } from '@lucide/vue';
import StatusBadge from '@/components/ui/StatusBadge.vue';
import { recentOrders } from '@/utils/mockData.js';

const columns = ['Bill No', 'Customer', 'Table', 'Type', 'Total', 'Status', 'Time'];

function billTypeClass(type) {
  if (type === 'Dine In') return 'bg-info/10 text-info';
  if (type === 'Takeaway') return 'bg-accent text-primary';
  return 'bg-secondary/10 text-secondary';
}
</script>
