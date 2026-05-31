import { defineStore } from 'pinia';
import { ref } from 'vue';
import { categoryApi } from '@/services/settingsApi';

export const useCategoryStore = defineStore('category', () => {
  const rows    = ref([]);
  const total   = ref(0);
  const pages   = ref(1);
  const loading = ref(false);
  const saving  = ref(false);
  const loader  = ref(false);


  async function fetchCategories(params) {
    if(loader.value) return;
    loading.value = true;
    try {
      loader.value = true;  
      const res = await categoryApi.list(params);
      rows.value  = res.data;
      total.value = res.total;
      pages.value = res.pages;
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

  return { rows, total, pages, loading, saving, fetchCategories, createCategory, updateCategory, deleteCategory };
});
