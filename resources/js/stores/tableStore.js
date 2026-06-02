import { defineStore } from 'pinia';
import { ref } from 'vue';
import { tableApi } from '@/services/settingsApi';

export const useTableStore = defineStore('table', () => {
  const rows = ref([]);
  const total = ref(0);
  const currentPage = ref(1);
  const lastPage = ref(1);
  const from = ref(0);
  const to = ref(0);
  const loading = ref(false);
  const saving = ref(false);
  const loader = ref(false);

  async function fetchTables(params) {
    if (loader.value) return;
    loading.value = true;
    try {
      loader.value = true;
      const res = await tableApi.list(params);
      rows.value = res.data;
      total.value = res.total;
      currentPage.value = res.current_page;
      lastPage.value = res.last_page;
      from.value = res.from || 0;
      to.value = res.to || 0;
      return res;
    } finally {
      loading.value = false;
      loader.value = false;
    }
  }

  async function createTable(data) {
    saving.value = true;
    try {
      await tableApi.create(data);
    } finally {
      saving.value = false;
    }
  }

  async function updateTable(id, data) {
    saving.value = true;
    try {
      await tableApi.update(id, data);
    } finally {
      saving.value = false;
    }
  }

  async function deleteTable(id) {
    await tableApi.remove(id);
  }

  return { rows, total, currentPage, lastPage, from, to, loading, saving, fetchTables, createTable, updateTable, deleteTable };
});
