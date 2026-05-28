<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Dashboard</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Tuesday, May 27, 2026 · Lunch service in progress</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full" style="color:var(--success);background:color-mix(in srgb, var(--success) 10%, transparent)">
          <span class="w-1.5 h-1.5 rounded-full pulse-dot" style="background:var(--success)" />
          Live
        </span>
      </div>
    </div>

    <!-- KPI Cards -->
    <DashboardKPIs />

    <!-- Charts Row -->
    <DashboardCharts />

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <TopSellingItems />
      <div class="xl:col-span-2">
        <RecentOrdersTable />
      </div>
    </div>
  </div>
</template>

<script setup>
import DashboardKPIs from '@/components/DashboardKPIs.vue';
import DashboardCharts from '@/components/DashboardCharts.vue';
import TopSellingItems from '@/components/TopSellingItems.vue';
import RecentOrdersTable from '@/components/RecentOrdersTable.vue';
</script>
