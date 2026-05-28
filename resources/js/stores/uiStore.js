import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUIStore = defineStore('ui', () => {
  const darkMode = ref(false);
  const sidebarCollapsed = ref(false);
  const mobileSidebarOpen = ref(false);

  function toggleDark() {
    darkMode.value = !darkMode.value;
    document.documentElement.classList.toggle('dark', darkMode.value);
  }

  function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
  }

  function openMobileSidebar() {
    mobileSidebarOpen.value = true;
  }

  function closeMobileSidebar() {
    mobileSidebarOpen.value = false;
  }

  return {
    darkMode, sidebarCollapsed, mobileSidebarOpen,
    toggleDark, toggleSidebar, openMobileSidebar, closeMobileSidebar,
  };
});
