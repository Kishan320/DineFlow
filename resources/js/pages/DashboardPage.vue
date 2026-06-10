<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Dashboard</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">{{ currentDateString }}</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full" style="color:var(--success);background:color-mix(in srgb, var(--success) 10%, transparent)">
          <span class="w-1.5 h-1.5 rounded-full pulse-dot" style="background:var(--success)" />
          Live
        </span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <DashboardKPIs :kpi-data="dashboardData.kpis" />

      <!-- Charts Row -->
      <DashboardCharts 
        :hourly-sales="dashboardData.hourlySales" 
        :weekly-sales="dashboardData.weeklySales" 
      />

      <!-- Bottom Row -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <TopSellingItems :items="dashboardData.topSellingItems" />
        <div class="xl:col-span-2">
          <RecentOrdersTable :orders="dashboardData.recentOrders" />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/axiosInstance.js';
import DashboardKPIs from '@/components/DashboardKPIs.vue';
import DashboardCharts from '@/components/DashboardCharts.vue';
import TopSellingItems from '@/components/TopSellingItems.vue';
import RecentOrdersTable from '@/components/RecentOrdersTable.vue';

const currentDateString = ref('');

const updateDateString = () => {
  const now = new Date();
  const dateStr = new Intl.DateTimeFormat('en-US', {
    weekday: 'long', month: 'short', day: 'numeric', year: 'numeric'
  }).format(now);
  
  const hour = now.getHours();
  let period = 'Night service';
  if (hour >= 6 && hour < 11) period = 'Breakfast service';
  else if (hour >= 11 && hour < 16) period = 'Lunch service';
  else if (hour >= 16 && hour < 23) period = 'Dinner service';
  
  currentDateString.value = `${dateStr} · ${period} in progress`;
};

const isLoading = ref(true);
const dashboardData = ref({
  kpis: [],
  hourlySales: [],
  weeklySales: [],
  topSellingItems: [],
  recentOrders: []
});

const fetchDashboardData = async () => {
  try {
    isLoading.value = true;
    const response = await api.get('/dashboard');
    dashboardData.value = response;
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  updateDateString();
  fetchDashboardData();
});
</script>
