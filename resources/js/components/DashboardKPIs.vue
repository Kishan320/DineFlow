<template>
  <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
    <div
      v-for="kpi in kpiData"
      :key="kpi.id"
      :class="[
        'border rounded-xl p-4 card-hover shadow-card',
        kpi.hero ? 'col-span-2 md:col-span-1' : '',
        kpi.id === 'kpi-pending' ? 'border-danger/30 bg-danger/5' : 'border-border',
      ]"
      style="background:var(--card)"
    >
      <div class="flex items-start justify-between mb-3">
        <div :class="['w-9 h-9 rounded-lg flex items-center justify-center', kpi.bg, kpi.color]">
          <component :is="kpi.icon" :size="20" />
        </div>
        <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', changeClass(kpi.changeType)]">
          {{ kpi.change }}
        </span>
      </div>
      <p class="text-2xl font-bold tabular-nums" style="color:var(--foreground)">{{ kpi.value }}</p>
      <p class="text-xs font-medium mt-1 uppercase tracking-wide" style="color:var(--muted-foreground)">{{ kpi.label }}</p>
      <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">{{ kpi.subtext }}</p>
    </div>
  </div>
</template>

<script setup>
import { ShoppingBag, DollarSign, Grid3X3, Users, TrendingUp, Clock } from '@lucide/vue';

function changeClass(type) {
  if (type === 'positive') return 'text-success bg-success/10';
  if (type === 'negative') return 'text-danger bg-danger/10';
  if (type === 'neutral') return 'text-info bg-info/10';
  if (type === 'alert') return 'text-danger bg-danger/10';
  return '';
}

const kpiData = [
  { id: 'kpi-orders', label: "Today's Orders", value: '124', change: '+12%', changeType: 'positive', subtext: 'vs yesterday 111', icon: ShoppingBag, color: 'text-primary', bg: 'bg-accent', hero: true },
  { id: 'kpi-revenue', label: "Today's Revenue", value: '$3,847', change: '+8.4%', changeType: 'positive', subtext: 'vs yesterday $3,549', icon: DollarSign, color: 'text-secondary', bg: 'bg-secondary/10', hero: true },
  { id: 'kpi-tables', label: 'Active Tables', value: '4 / 10', change: '40%', changeType: 'neutral', subtext: '4 occupied, 4 available', icon: Grid3X3, color: 'text-info', bg: 'bg-info/10' },
  { id: 'kpi-customers', label: 'Customers Today', value: '89', change: '+5', changeType: 'positive', subtext: 'since last hour', icon: Users, color: 'text-secondary', bg: 'bg-secondary/10' },
  { id: 'kpi-aov', label: 'Avg Order Value', value: '$31.02', change: '-2.1%', changeType: 'negative', subtext: 'vs $31.68 yesterday', icon: TrendingUp, color: 'text-warning', bg: 'bg-warning/10' },
  { id: 'kpi-pending', label: 'Pending Bills', value: '3', change: 'Action needed', changeType: 'alert', subtext: '$103.73 outstanding', icon: Clock, color: 'text-danger', bg: 'bg-danger/10' },
];
</script>
