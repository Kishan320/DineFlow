import { defineStore } from 'pinia';
import { ref } from 'vue';
import { taxApi } from '@/services/settingsApi';

export const useTaxStore = defineStore('tax', () => {
  const rows = ref([]);
  const total = ref(0);
  const perPage = ref(10);
  const currentPage = ref(1);
  const lastPage = ref(1);
  const from = ref(0);
  const to = ref(0);
  const loading = ref(false);
  const saving = ref(false);
  const loader = ref(false);

  async function fetchTaxes(params) {
    if (loader.value) return;
    loading.value = true;
    try {
      loader.value = true;
      const res = await taxApi.list(params);
      rows.value = res.data;
      total.value = res.total;
      perPage.value = res.per_page;
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

  async function createTax(data) {
    saving.value = true;
    try {
      await taxApi.create(data);
    } finally {
      saving.value = false;
    }
  }

  async function updateTax(id, data) {
    saving.value = true;
    try {
      await taxApi.update(id, data);
    } finally {
      saving.value = false;
    }
  }

  async function deleteTax(id) {
    await taxApi.remove(id);
  }

  return { rows, total, currentPage, lastPage, from, to, perPage, loading, saving, fetchTaxes, createTax, updateTax, deleteTax };
});
