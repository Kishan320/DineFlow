<template>
  <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
    <div class="xl:col-span-3 border rounded-xl p-5 shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-base font-semibold" style="color:var(--foreground)">Hourly Sales Today</h2>
          <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">Order volume and revenue by hour</p>
        </div>
        <span class="text-xs font-medium px-2.5 py-1 rounded-lg" style="color:var(--muted-foreground);background:var(--muted)">May 27, 2026</span>
      </div>
      <HourlySalesChart :data="hourlySales" />
    </div>
    <div class="xl:col-span-2 border rounded-xl p-5 shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-base font-semibold" style="color:var(--foreground)">Weekly Revenue</h2>
          <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">Revenue this week by day</p>
        </div>
      </div>
      <WeeklyRevenueChart :data="weeklySales" />
    </div>
  </div>
</template>

<script setup>
import HourlySalesChart from '@/components/HourlySalesChart.vue';
import WeeklyRevenueChart from '@/components/WeeklyRevenueChart.vue';

const props = defineProps({
  hourlySales: {
    type: Array,
    required: true,
    default: () => []
  },
  weeklySales: {
    type: Array,
    required: true,
    default: () => []
  }
});
</script>
