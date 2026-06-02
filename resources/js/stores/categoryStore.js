import { defineStore } from 'pinia';
import { ref } from 'vue';
import { categoryApi } from '@/services/settingsApi';

export const useCategoryStore = defineStore('category', () => {
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

  async function fetchCategories(params) {
    if (loader.value) return;
    loading.value = true;
    try {
      loader.value = true;
      const res = await categoryApi.list(params);
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

  async function createCategory(data) {
    saving.value = true;
    try {
      await categoryApi.create(data);
    } finally {
      saving.value = false;
    }
  }

  async function updateCategory(id, data) {
    saving.value = true;
    try {
      await categoryApi.update(id, data);
    } finally {
      saving.value = false;
    }
  }

  async function deleteCategory(id) {
    await categoryApi.remove(id);
  }

  return { rows, total, currentPage, lastPage, from, to, perPage, loading, saving, fetchCategories, createCategory, updateCategory, deleteCategory };
});
