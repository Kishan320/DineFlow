<template>
  <apexchart type="area" height="220" :options="chartOptions" :series="series" />
</template>

<script setup>
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
  data: {
    type: Array,
    required: true,
    default: () => []
  }
});

const apexchart = VueApexCharts;

const series = computed(() => [
  { name: 'revenue', data: props.data.map(d => d.revenue) },
  { name: 'orders', data: props.data.map(d => d.orders) },
]);

const chartOptions = computed(() => ({
  chart: { toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent' },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 2 },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.15,
      opacityTo: 0,
      stops: [0, 95],
    },
  },
  colors: ['#E85D26', '#2D6A4F'],
  xaxis: {
    categories: props.data.map(d => d.hour),
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: { style: { colors: '#78716C', fontSize: '11px' } },
  },
  yaxis: {
    labels: { style: { colors: '#78716C', fontSize: '11px' } },
  },
  grid: {
    borderColor: '#E8E5E0',
    strokeDashArray: 3,
    xaxis: { lines: { show: false } },
  },
  tooltip: {
    theme: 'light',
    style: { fontSize: '12px' },
  },
  legend: { position: 'bottom', fontSize: '12px' },
}));
</script>
