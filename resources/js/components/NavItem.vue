<template>
  <div v-if="hasChildren">
    <button
      @click="$emit('toggle', item.id)"
      :class="[
        'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium',
        'transition-all duration-150 group relative',
        childActive ? 'bg-accent text-primary' : 'hover:bg-muted',
        collapsed ? 'justify-center' : '',
      ]"
      :style="childActive ? 'color:var(--primary)' : 'color:var(--muted-foreground)'"
    >
      <component :is="iconComponent" :size="18" class="flex-shrink-0" />
      <template v-if="!collapsed">
        <span class="flex-1 text-left">{{ item.label }}</span>
        <component :is="expanded ? ChevronDown : ChevronRight" :size="14" />
      </template>
      <!-- Tooltip when collapsed -->
      <span v-if="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-foreground text-background text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50" style="background:var(--foreground);color:var(--background)">
        {{ item.label }}
      </span>
    </button>
    <div v-if="!collapsed && expanded" class="mt-1 ml-4 pl-3 border-l space-y-0.5" style="border-color:var(--border)">
      <NavItem
        v-for="child in item.children"
        :key="child.id"
        :item="child"
        :collapsed="false"
        :current-path="currentPath"
        :expanded-groups="expandedGroups"
        @toggle="$emit('toggle', $event)"
      />
    </div>
  </div>

  <RouterLink
    v-else
    :to="item.href || '#'"
    :class="[
      'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium',
      'transition-all duration-150 group relative',
      isActive ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-muted',
      collapsed ? 'justify-center' : '',
    ]"
    :style="isActive ? 'background:var(--primary);color:var(--primary-foreground)' : 'color:var(--muted-foreground)'"
  >
    <component :is="iconComponent" :size="18" class="flex-shrink-0" />
    <template v-if="!collapsed">
      <span class="flex-1">{{ item.label }}</span>
      <span
        v-if="item.badge !== undefined"
        :class="['text-xs font-semibold px-1.5 py-0.5 rounded-full', isActive ? 'bg-white/20 text-white' : 'bg-primary/10 text-primary']"
      >{{ item.badge }}</span>
    </template>
    <span v-if="collapsed" class="absolute left-full ml-2 px-2 py-1 text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50" style="background:var(--foreground);color:var(--background)">
      {{ item.label }}{{ item.badge !== undefined ? ` (${item.badge})` : '' }}
    </span>
  </RouterLink>
</template>

<script setup>
import { computed, defineAsyncComponent } from 'vue';
import { RouterLink } from 'vue-router';
import {
  LayoutDashboard, ShoppingCart, BarChart3, Settings, ChevronDown, ChevronRight,
  Package, Tag, Users, Grid3X3, UserCheck, Percent, Store, FileText,
  TrendingUp, Printer, PieChart,
} from '@lucide/vue';

const icons = {
  LayoutDashboard, ShoppingCart, BarChart3, Settings, Package, Tag, Users,
  Grid3X3, UserCheck, Percent, Store, FileText, TrendingUp, Printer, PieChart,
};

const props = defineProps({
  item: Object,
  collapsed: Boolean,
  currentPath: String,
  expandedGroups: Object,
});

defineEmits(['toggle']);

const hasChildren = computed(() => props.item.children?.length > 0);
const expanded = computed(() => props.expandedGroups.has(props.item.id));
const iconComponent = computed(() => icons[props.item.iconName] || FileText);

const isActive = computed(() => {
  if (!props.item.href) return false;
  if (props.item.href === '/') return props.currentPath === '/';
  return props.currentPath.startsWith(props.item.href);
});

const childActive = computed(() => {
  if (!hasChildren.value) return false;
  return props.item.children.some(child => {
    if (!child.href) return false;
    if (child.href === '/') return props.currentPath === '/';
    return props.currentPath.startsWith(child.href);
  });
});
</script>
