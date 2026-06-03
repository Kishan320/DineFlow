<template>
  <header class="h-10 border-b flex items-center px-4 gap-3 flex-shrink-0 z-30 sticky top-0" style="background:var(--card);border-color:var(--border)">
    <!-- Mobile menu -->
    <button @click="$emit('menu-click')" class="lg:hidden p-2 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
      <MenuIcon :size="20" />
    </button>

    <!-- Desktop collapse toggle -->
    <button @click="$emit('collapse-toggle')" class="hidden lg:flex p-2 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
      <component :is="collapsed ? PanelLeftOpen : PanelLeftClose" :size="18" />
    </button>

    <!-- Search -->
    <div class="flex-1 max-w-md hidden sm:flex items-center gap-2 rounded-lg px-3 py-2" style="background:var(--muted)">
      <SearchIcon :size="16" class="flex-shrink-0" style="color:var(--muted-foreground)" />
      <input
        type="text"
        placeholder="Search orders, items, customers..."
        class="flex-1 bg-transparent text-sm outline-none"
        style="color:var(--foreground)"
      />
      <kbd class="text-xs rounded px-1.5 py-0.5 hidden md:block" style="color:var(--muted-foreground);background:var(--background);border:1px solid var(--border)">⌘K</kbd>
    </div>

    <div class="flex-1 sm:hidden" />

    <!-- Actions -->
    <div class="flex items-center gap-1">
      <!-- Today's Sale -->
      <button
        @click="todaysSaleOpen = true"
        class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-colors hover:bg-muted"
        style="border-color:var(--border);color:var(--primary)"
      >
        <ShoppingBagIcon :size="14" /> Todays Sale
      </button>

      <!-- Dark mode -->
      <button @click="uiStore.toggleDark()" class="p-2 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
        <component :is="uiStore.darkMode ? SunIcon : MoonIcon" :size="18" />
      </button>

      <!-- Notifications -->
      <div class="relative">
        <button @click="toggleNotif" class="p-2 rounded-lg hover:bg-muted transition-colors relative" style="color:var(--muted-foreground)">
          <BellIcon :size="18" />
          <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full pulse-dot" style="background:var(--primary)" />
        </button>
        <div v-if="notifOpen" class="absolute right-0 top-full mt-2 w-80 border rounded-xl shadow-lg z-50 overflow-hidden fade-in" style="background:var(--card);border-color:var(--border)">
          <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:var(--border)">
            <span class="font-semibold text-sm" style="color:var(--foreground)">Notifications</span>
            <span class="text-xs font-medium cursor-pointer hover:underline" style="color:var(--primary)">Mark all read</span>
          </div>
          <div
            v-for="n in notifications"
            :key="n.id"
            :class="['px-4 py-3 border-b last:border-0 hover:bg-muted transition-colors cursor-pointer', n.unread ? 'bg-accent/30' : '']"
            style="border-color:var(--border)"
          >
            <p class="text-sm" style="color:var(--foreground)">{{ n.text }}</p>
            <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">{{ n.time }}</p>
          </div>
        </div>
      </div>

      <!-- Profile -->
      <div class="relative">
        <button @click="toggleProfile" class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-muted transition-colors">
          <div class="w-7 h-7 rounded-full gradient-primary flex items-center justify-center">
            <span class="text-white text-xs font-bold">{{ authStore.userInitials }}</span>
          </div>
          <span class="text-sm font-medium hidden md:block" style="color:var(--foreground)">{{ authStore.userName }}</span>
          <ChevronDownIcon :size="14" class="hidden md:block" style="color:var(--muted-foreground)" />
        </button>
        <div v-if="profileOpen" class="absolute right-0 top-full mt-2 w-48 border rounded-xl shadow-lg z-50 overflow-hidden fade-in" style="background:var(--card);border-color:var(--border)">
          <div class="px-4 py-3 border-b" style="border-color:var(--border)">
            <p class="text-sm font-semibold" style="color:var(--foreground)">{{ authStore.userName }}</p>
            <p class="text-xs" style="color:var(--muted-foreground)">{{ authStore.userEmail }}</p>
          </div>
          <div class="p-1">
            <button v-for="item in profileMenu" :key="item" class="w-full text-left px-3 py-2 text-sm hover:bg-muted rounded-lg transition-colors" style="color:var(--foreground)">
              {{ item }}
            </button>
            <button @click="handleLogout" class="w-full text-left px-3 py-2 text-sm rounded-lg transition-colors hover:bg-danger/10" style="color:var(--danger)">
              Sign Out
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>

  <TodaysSaleModal v-if="todaysSaleOpen" @close="todaysSaleOpen = false" />
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useUIStore } from '@/stores/uiStore.js';
import { useAuthStore } from '@/stores/authStore.js';
import {
  Menu as MenuIcon, PanelLeftClose, PanelLeftOpen, Search as SearchIcon,
  Bell as BellIcon, Sun as SunIcon, Moon as MoonIcon, ChevronDown as ChevronDownIcon,
  ShoppingBag as ShoppingBagIcon,
} from '@lucide/vue';
import TodaysSaleModal from '@/components/TodaysSaleModal.vue';
import { useSnackbar } from '@/composables/useSnackbar';

defineProps({ collapsed: Boolean });
defineEmits(['menu-click', 'collapse-toggle']);

const router      = useRouter();
const uiStore     = useUIStore();
const authStore   = useAuthStore();
const snackbar    = useSnackbar();
const notifOpen   = ref(false);
const profileOpen = ref(false);
const todaysSaleOpen = ref(false);

function toggleNotif() { notifOpen.value = !notifOpen.value; profileOpen.value = false; }
function toggleProfile() { profileOpen.value = !profileOpen.value; notifOpen.value = false; }

async function handleLogout() {
  profileOpen.value = false;
  await authStore.logout();
  snackbar.success('Signed out successfully');
  router.push('/login');
}

const notifications = [
  { id: 'notif-1', text: 'Table 7 order ready for billing', time: '2 min ago', unread: true },
  { id: 'notif-2', text: 'New order placed — Table 3', time: '8 min ago', unread: true },
  { id: 'notif-3', text: 'Daily sales report generated', time: '1 hr ago', unread: false },
];

const profileMenu = ['Profile Settings', 'Restaurant Settings', 'Help & Support'];
</script>
