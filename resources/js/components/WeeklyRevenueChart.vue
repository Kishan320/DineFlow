<template>
  <apexchart type="bar" height="220" :options="chartOptions" :series="series" />
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

const maxVal = computed(() => {
  if (!props.data || props.data.length === 0) return 0;
  return Math.max(...props.data.map(d => d.revenue));
});

const series = computed(() => [{ name: 'Revenue', data: props.data.map(d => d.revenue) }]);

const chartOptions = computed(() => ({
  chart: { toolbar: { show: false }, background: 'transparent' },
  plotOptions: {
    bar: {
      borderRadius: 6,
      columnWidth: '55%',
      distributed: true,
    },
  },
  colors: props.data.map(d => d.revenue === maxVal.value ? '#E85D26' : '#E8E5E0'),
  dataLabels: { enabled: false },
  legend: { show: false },
  xaxis: {
    categories: props.data.map(d => d.day),
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
    y: { formatter: (val) => `$${val.toLocaleString()}` },
  },
}));
</script>
