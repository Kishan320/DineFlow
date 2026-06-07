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
      <span v-if="!collapsed" class="font-bold text-base text-foreground tracking-tight" style="color:var(--foreground)">DineFlow</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto scrollbar-thin p-2 space-y-0.5">
      <template v-for="item in visibleNavItems" :key="item.id">
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
          <p class="text-xs" style="color:var(--muted-foreground)">{{ authStore.primaryRole }}</p>
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
        <span class="font-bold text-base" style="color:var(--foreground)">DineFlow</span>
      </div>
      <button @click="$emit('mobile-close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
        <XIcon :size="18" />
      </button>
    </div>
    <nav class="flex-1 overflow-y-auto scrollbar-thin p-2 space-y-0.5">
      <template v-for="item in visibleNavItems" :key="item.id">
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
          <p class="text-xs" style="color:var(--muted-foreground)">{{ authStore.primaryRole }}</p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLogo from '@/components/ui/AppLogo.vue';
import NavItem from '@/components/NavItem.vue';
import { X as XIcon } from '@lucide/vue';
import { useAuthStore } from '@/stores/authStore.js';
import { usePermission } from '@/composables/usePermission.js';

defineProps({
  collapsed: Boolean,
  mobileOpen: Boolean,
  currentPath: String,
});

defineEmits(['mobile-close']);

const expandedGroups = ref(new Set(['nav-workarea']));
const authStore = useAuthStore();
const { can, canAny } = usePermission();

function toggleGroup(id) {
  if (expandedGroups.value.has(id)) {
    expandedGroups.value.delete(id);
  } else {
    expandedGroups.value.add(id);
  }
}

/** Full nav item definitions — filtered by permissions below. */
const allNavItems = [
  {
    id: 'nav-dashboard',
    label: 'Dashboard',
    iconName: 'LayoutDashboard',
    href: '/',
    permission: 'dashboard.view',
  },
  {
    id: 'nav-pos',
    label: 'POS System',
    iconName: 'ShoppingCart',
    href: '/pos-system',
    permission: 'pos.access',
  },
  {
    id: 'nav-workarea',
    label: 'Work Area',
    iconName: 'BarChart3',
    children: [
      { id: 'nav-sales', label: 'Sales', iconName: 'TrendingUp', href: '/sales-management', permission: 'sales.view' },
    ],
  },
  {
    id: 'nav-settings',
    label: 'Settings',
    iconName: 'Settings',
    children: [
      { id: 'nav-items',      label: 'Items',               iconName: 'Package',   href: '/settings/items',      permission: 'items.view' },
      { id: 'nav-categories', label: 'Item Categories',     iconName: 'Tag',       href: '/settings/categories', permission: 'categories.view' },
      { id: 'nav-customers',  label: 'Customers',           iconName: 'Users',     href: '/settings/customers',  permission: 'customers.view' },
      { id: 'nav-tables',     label: 'Tables',              iconName: 'Grid3X3',   href: '/settings/tables',     permission: 'tables.view' },
      { id: 'nav-waiters',    label: 'Waiters',             iconName: 'UserCheck', href: '/settings/waiters',    permission: 'waiters.view' },
      { id: 'nav-taxes',      label: 'Taxes',               iconName: 'Percent',   href: '/settings/taxes',      permission: 'taxes.view' },
      { id: 'nav-restaurant', label: 'Restaurant Settings', iconName: 'Store',     href: '/settings/restaurant', permission: 'settings.view' },
    ],
  },
  {
    id: 'nav-reports',
    label: 'Reports',
    iconName: 'FileText',
    children: [
      { id: 'nav-cashier',    label: 'Cashier Report',     iconName: 'BarChart3',  href: '/reports/cashier',   permission: 'reports.cashier' },
      { id: 'nav-daily',      label: 'Daily Sales',        iconName: 'TrendingUp', href: '/reports/daily',     permission: 'reports.daily_sales' },
      { id: 'nav-detailed',   label: 'Detailed Sales',     iconName: 'FileText',   href: '/reports/detailed',  permission: 'reports.detailed_sales' },
      { id: 'nav-print',      label: 'Sales Print Report', iconName: 'Printer',    href: '/reports/print',     permission: 'reports.detailed_sales' },
      { id: 'nav-itemwise',   label: 'Item Wise Sales',    iconName: 'PieChart',   href: '/reports/item-wise', permission: 'reports.item_wise' },
    ],
  },
  {
    id: 'nav-admin',
    label: 'Administration',
    iconName: 'ShieldCheck',
    children: [
      { id: 'nav-users', label: 'Users',  iconName: 'Users',  href: '/admin/users',  permission: 'users.view' },
      { id: 'nav-roles', label: 'Roles',  iconName: 'Shield', href: '/admin/roles',  permission: 'roles.view' },
    ],
  },
];

/** Filter nav items based on the current user's permissions. */
const visibleNavItems = computed(() => {
  return allNavItems
    .map(item => {
      // Item with children — filter children by permission
      if (item.children) {
        const visibleChildren = item.children.filter(child =>
          !child.permission || can(child.permission)
        );
        // Hide the parent group if no children are visible
        if (visibleChildren.length === 0) return null;
        return { ...item, children: visibleChildren };
      }

      // Leaf item — check its permission
      if (item.permission && !can(item.permission)) return null;
      return item;
    })
    .filter(Boolean);
});
</script>
