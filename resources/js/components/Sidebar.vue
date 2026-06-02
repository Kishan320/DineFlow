<template>
  <!-- Desktop sidebar -->
  <aside
    :class="[
      'hidden lg:flex flex-col bg-card border-r border-border sidebar-transition flex-shrink-0',
      collapsed ? 'w-16' : 'w-60'
    ]"
  >
    <!-- Logo -->
    <div :class="['flex items-center gap-3 px-4 py-4 border-b border-border', collapsed ? 'justify-center px-2' : '']">
      <AppLogo :size="32" />
      <span v-if="!collapsed" class="font-bold text-base text-foreground tracking-tight" style="color:var(--foreground)">RestaurantMS</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto scrollbar-thin p-2 space-y-0.5">
      <template v-for="item in navItems" :key="item.id">
        <NavItem :item="item" :collapsed="collapsed" :current-path="currentPath" :expanded-groups="expandedGroups" @toggle="toggleGroup" />
      </template>
    </nav>

    <!-- Bottom user -->
    <div class="p-2 border-t border-border">
      <div :class="['flex items-center gap-3 px-3 py-2.5 rounded-lg', collapsed ? 'justify-center' : '']">
        <div class="w-7 h-7 rounded-full gradient-primary flex items-center justify-center flex-shrink-0">
          <span class="text-white text-xs font-bold">{{ authStore.userInitials }}</span>
        </div>
        <div v-if="!collapsed" class="flex-1 min-w-0">
          <p class="text-sm font-semibold truncate" style="color:var(--foreground)">{{ authStore.userName }}</p>
          <p class="text-xs" style="color:var(--muted-foreground)">Administrator</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- Mobile drawer -->
  <aside
    :class="[
      'fixed top-0 left-0 h-full w-72 border-r border-border z-50',
      'transform transition-transform duration-300 lg:hidden flex flex-col',
      mobileOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
    style="background:var(--card);border-color:var(--border)"
  >
    <div class="flex items-center justify-between px-4 py-4 border-b border-border">
      <div class="flex items-center gap-3">
        <AppLogo :size="32" />
        <span class="font-bold text-base" style="color:var(--foreground)">RestaurantMS</span>
      </div>
      <button @click="$emit('mobile-close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
        <XIcon :size="18" />
      </button>
    </div>
    <nav class="flex-1 overflow-y-auto scrollbar-thin p-2 space-y-0.5">
      <template v-for="item in navItems" :key="item.id">
        <NavItem :item="item" :collapsed="false" :current-path="currentPath" :expanded-groups="expandedGroups" @toggle="toggleGroup" />
      </template>
    </nav>
    <div class="p-3 border-t border-border">
      <div class="flex items-center gap-3 px-3 py-2">
        <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center">
          <span class="text-white text-xs font-bold">{{ authStore.userInitials }}</span>
        </div>
        <div>
          <p class="text-sm font-semibold" style="color:var(--foreground)">{{ authStore.userName }}</p>
          <p class="text-xs" style="color:var(--muted-foreground)">Administrator</p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import AppLogo from '@/components/ui/AppLogo.vue';
import NavItem from '@/components/NavItem.vue';
import { X as XIcon } from '@lucide/vue';
import { useAuthStore } from '@/stores/authStore.js';

defineProps({
  collapsed: Boolean,
  mobileOpen: Boolean,
  currentPath: String,
});

defineEmits(['mobile-close']);

const expandedGroups = ref(new Set(['nav-workarea']));
const authStore = useAuthStore();

function toggleGroup(id) {
  if (expandedGroups.value.has(id)) {
    expandedGroups.value.delete(id);
  } else {
    expandedGroups.value.add(id);
  }
}

const navItems = [
  { id: 'nav-dashboard', label: 'Dashboard', iconName: 'LayoutDashboard', href: '/' },
  { id: 'nav-pos', label: 'POS System', iconName: 'ShoppingCart', href: '/pos-system', badge: 3 },
  {
    id: 'nav-workarea', label: 'Work Area', iconName: 'BarChart3',
    children: [
      { id: 'nav-sales', label: 'Sales', iconName: 'TrendingUp', href: '/sales-management' },
    ],
  },
  {
    id: 'nav-settings', label: 'Settings', iconName: 'Settings',
    children: [
      { id: 'nav-items', label: 'Items', iconName: 'Package', href: '/settings/items' },
      { id: 'nav-categories', label: 'Item Categories', iconName: 'Tag', href: '/settings/categories' },
      { id: 'nav-customers', label: 'Customers', iconName: 'Users', href: '/settings/customers' },
      { id: 'nav-tables', label: 'Tables', iconName: 'Grid3X3', href: '/settings/tables' },
      { id: 'nav-waiters', label: 'Waiters', iconName: 'UserCheck', href: '/settings/waiters' },
      { id: 'nav-taxes', label: 'Taxes', iconName: 'Percent', href: '/settings/taxes' },
      { id: 'nav-restaurant', label: 'Restaurant Settings', iconName: 'Store', href: '/settings/restaurant' },
    ],
  },
  {
    id: 'nav-reports', label: 'Reports', iconName: 'FileText',
    children: [
      { id: 'nav-cashier', label: 'Cashier Report', iconName: 'BarChart3', href: '/reports/cashier' },
      { id: 'nav-daily', label: 'Daily Sales', iconName: 'TrendingUp', href: '/reports/daily' },
      { id: 'nav-detailed', label: 'Detailed Sales', iconName: 'FileText', href: '/reports/detailed' },
      { id: 'nav-print', label: 'Sales Print Report', iconName: 'Printer', href: '/reports/print' },
      { id: 'nav-itemwise', label: 'Item Wise Sales', iconName: 'PieChart', href: '/reports/item-wise' },
    ],
  },
];
</script>
