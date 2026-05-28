<template>
  <div class="flex h-screen overflow-hidden" style="background:var(--background)">
    <!-- Mobile overlay -->
    <div
      v-if="uiStore.mobileSidebarOpen"
      class="fixed inset-0 bg-black/40 z-40 lg:hidden fade-in"
      @click="uiStore.closeMobileSidebar()"
    />

    <!-- Sidebar -->
    <Sidebar
      :collapsed="uiStore.sidebarCollapsed"
      :mobile-open="uiStore.mobileSidebarOpen"
      :current-path="route.path"
      @mobile-close="uiStore.closeMobileSidebar()"
    />

    <!-- Main content area -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden transition-all duration-300">
      <Topbar
        :collapsed="uiStore.sidebarCollapsed"
        @menu-click="uiStore.openMobileSidebar()"
        @collapse-toggle="uiStore.toggleSidebar()"
      />
      <!-- For POS: flex column so child fills remaining height. For others: scroll -->
      <main
        v-if="isPOS"
        class="flex flex-col flex-1 min-h-0 overflow-hidden"
      >
        <RouterView />
      </main>
      <main v-else class="flex-1 overflow-auto scrollbar-thin">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useUIStore } from '@/stores/uiStore.js';
import Sidebar from '@/components/Sidebar.vue';
import Topbar from '@/components/Topbar.vue';

const route = useRoute();
const uiStore = useUIStore();
const isPOS = computed(() => route.path === '/pos-system');
</script>
