<template>
  <apexchart type="bar" height="220" :options="chartOptions" :series="series" />
</template>

<script setup>
import VueApexCharts from 'vue3-apexcharts';
import { weeklySalesData } from '@/utils/mockData.js';

const apexchart = VueApexCharts;

const maxVal = Math.max(...weeklySalesData.map(d => d.revenue));

const series = [{ name: 'Revenue', data: weeklySalesData.map(d => d.revenue) }];

const chartOptions = {
  chart: { toolbar: { show: false }, background: 'transparent' },
  plotOptions: {
    bar: {
      borderRadius: 6,
      columnWidth: '55%',
      distributed: true,
    },
  },
  colors: weeklySalesData.map(d => d.revenue === maxVal ? '#E85D26' : '#E8E5E0'),
  dataLabels: { enabled: false },
  legend: { show: false },
  xaxis: {
    categories: weeklySalesData.map(d => d.day),
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
};
</script>
