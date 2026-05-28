<template>
  <div class="border rounded-xl p-5 shadow-card" style="background:var(--card);border-color:var(--border)">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-base font-semibold" style="color:var(--foreground)">Top Selling Items</h2>
        <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">Today's best performers</p>
      </div>
      <TrendingUpIcon :size="16" style="color:var(--primary)" />
    </div>
    <div class="space-y-4">
      <div v-for="(item, index) in topSellingItems" :key="item.id" class="flex items-center gap-3">
        <span class="text-xs font-bold w-4 flex-shrink-0" style="color:var(--muted-foreground)">{{ index + 1 }}</span>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-1">
            <p class="text-sm font-medium truncate" style="color:var(--foreground)">{{ item.name }}</p>
            <span class="text-sm font-bold tabular-nums ml-2 flex-shrink-0" style="color:var(--foreground)">{{ item.quantity }}x</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background:var(--muted)">
              <div
                class="h-full rounded-full"
                :style="{
                  width: `${(item.quantity / maxQty) * 100}%`,
                  background: index === 0 ? 'var(--primary)' : 'var(--secondary)',
                  opacity: 1 - index * 0.1,
                }"
              />
            </div>
            <span class="text-xs tabular-nums w-14 text-right" style="color:var(--muted-foreground)">${{ item.revenue.toFixed(0) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { TrendingUp as TrendingUpIcon } from '@lucide/vue';
import { topSellingItems } from '@/utils/mockData.js';

const maxQty = computed(() => Math.max(...topSellingItems.map(i => i.quantity)));
</script>
