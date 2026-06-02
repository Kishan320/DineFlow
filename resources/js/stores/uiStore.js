import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUIStore = defineStore('ui', () => {
  const darkMode = ref(localStorage.getItem('dark_mode') === '1');
  const sidebarCollapsed = ref(false);
  const mobileSidebarOpen = ref(false);

  // Apply on store init so dark mode survives refresh
  document.documentElement.classList.toggle('dark', darkMode.value);

  function toggleDark() {
    darkMode.value = !darkMode.value;
    document.documentElement.classList.toggle('dark', darkMode.value);
    localStorage.setItem('dark_mode', darkMode.value ? '1' : '0');
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
