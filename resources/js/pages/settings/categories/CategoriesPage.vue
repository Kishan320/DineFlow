<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Item Category List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Item Categories</p>
      </div>
      <button
        @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)"
      >
        <PlusIcon :size="15" /> Add Category
      </button>
    </div>

    <!-- Category List -->
    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Category List</h2>
      </div>

      <!-- Controls -->
      <div class="flex items-center gap-3 px-4 py-3 border-b" style="border-color:var(--border)">
        <select v-model.number="pageSize" @change="page = 1" class="border rounded-lg px-2 py-1.5 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option v-for="n in [10, 25, 50]" :key="n" :value="n">{{ n }}</option>
        </select>
        <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 flex-1 max-w-xs" style="background:var(--muted)">
          <SearchIcon :size="13" style="color:var(--muted-foreground)" />
          <input v-model="search" @input="page = 1" type="text" placeholder="Type to Search..." class="flex-1 bg-transparent text-xs outline-none" style="color:var(--foreground)" />
          <button v-if="search" @click="search = ''" style="color:var(--muted-foreground)"><XIcon :size="12" /></button>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b" style="border-color:var(--border);background:color-mix(in srgb, var(--muted) 30%, transparent)">
              <th
                v-for="col in columns" :key="col.key"
                @click="toggleSort(col.key)"
                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer select-none"
                style="color:var(--muted-foreground)"
              >
                <div class="flex items-center gap-1">
                  {{ col.label }}
                  <component :is="sortIcon(col.key)" :size="11" :style="sortKey === col.key ? 'color:var(--primary)' : 'color:var(--muted-foreground);opacity:0.4'" />
                </div>
              </th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:var(--muted-foreground)">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="paginated.length === 0">
              <td :colspan="columns.length + 1" class="px-4 py-12 text-center text-sm" style="color:var(--muted-foreground)">No category records found</td>
            </tr>
            <tr
              v-for="cat in paginated" :key="cat.id"
              class="border-b last:border-0 transition-colors hover:bg-muted/40"
              style="border-color:var(--border)"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs font-medium" style="color:var(--foreground)">{{ cat.category_name }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--muted-foreground)">{{ cat.description }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-xs font-medium" style="color:var(--foreground)">{{ cat.last_accessed_by }}</span>
                  <span class="text-xs" style="color:var(--muted-foreground)">{{ cat.updated_at ? new Date(cat.updated_at).toLocaleString('en-IN') : '' }}</span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <button @click="openEdit(cat)" class="p-1.5 rounded-lg hover:bg-info/10 transition-colors" style="color:var(--primary)" title="Edit">
                    <EditIcon :size="13" />
                  </button>
                  <button @click="openDelete(cat)" class="p-1.5 rounded-lg hover:bg-danger/10 transition-colors" style="color:var(--danger)" title="Delete">
                    <Trash2Icon :size="13" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between gap-3 px-4 py-3 border-t" style="border-color:var(--border)">
        <span class="text-xs" style="color:var(--muted-foreground)">
          Showing {{ filtered.length === 0 ? 0 : (page - 1) * pageSize + 1 }} to {{ Math.min(page * pageSize, filtered.length) }} of {{ filtered.length }} entries
        </span>
        <div class="flex items-center gap-1">
          <button @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">
            <ChevronLeftIcon :size="13" />
          </button>
          <button
            v-for="pn in pageNumbers" :key="pn" @click="page = pn"
            class="w-7 h-7 rounded-lg text-xs font-medium transition-colors"
            :style="page === pn ? 'background:var(--primary);color:var(--primary-foreground)' : 'color:var(--muted-foreground)'"
          >{{ pn }}</button>
          <button @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages || totalPages === 0" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">
            <ChevronRightIcon :size="13" />
          </button>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <CategoryFormModal
      v-if="showFormModal"
      :category="editingCategory"
      @close="closeFormModal"
      @save="saveCategory"
    />
    <CategoryDeleteModal
      v-if="deletingCategory"
      :category="deletingCategory"
      @close="deletingCategory = null"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
  Search as SearchIcon, X as XIcon, Plus as PlusIcon,
  Edit as EditIcon, Trash2 as Trash2Icon,
  ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon,
  ArrowUpDown, ArrowUp, ArrowDown,
} from '@lucide/vue';
import CategoryFormModal   from './CategoryFormModal.vue';
import CategoryDeleteModal from './CategoryDeleteModal.vue';
import { categoryApi } from '@/services/settingsApi';

const search   = ref('');
const sortKey  = ref('category_name');
const sortDir  = ref('asc');
const page     = ref(1);
const pageSize = ref(25);

const showFormModal     = ref(false);
const editingCategory   = ref(null);
const deletingCategory  = ref(null);
const loading           = ref(false);

const categories = ref([]);

onMounted(async () => {
  loading.value = true;
  try {
    const res = await categoryApi.getAll();
    categories.value = Array.isArray(res) ? res : (res.data ?? []);
  } finally { loading.value = false; }
});

const columns = [
  { key: 'category_name',   label: 'Category Name' },
  { key: 'description',     label: 'Description' },
  { key: 'last_accessed_by', label: 'Last Accessed By' },
];

const filtered = computed(() => {
  let data = categories.value.filter(c =>
    !search.value ||
    (c.category_name || '').toLowerCase().includes(search.value.toLowerCase()) ||
    (c.description || '').toLowerCase().includes(search.value.toLowerCase())
  );
  if (sortKey.value) {
    data = [...data].sort((a, b) => {
      const av = a[sortKey.value], bv = b[sortKey.value];
      return sortDir.value === 'asc' ? String(av).localeCompare(String(bv)) : String(bv).localeCompare(String(av));
    });
  }
  return data;
});

const totalPages  = computed(() => Math.max(1, Math.ceil(filtered.value.length / pageSize.value)));
const paginated   = computed(() => filtered.value.slice((page.value - 1) * pageSize.value, page.value * pageSize.value));
const pageNumbers = computed(() => {
  const total = totalPages.value, cur = page.value, count = Math.min(5, total);
  const start = cur <= 3 ? 1 : cur >= total - 2 ? Math.max(1, total - 4) : cur - 2;
  return Array.from({ length: count }, (_, i) => start + i);
});

function toggleSort(key) {
  if (sortKey.value === key) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  else { sortKey.value = key; sortDir.value = 'asc'; }
}
function sortIcon(key) {
  if (sortKey.value !== key) return ArrowUpDown;
  return sortDir.value === 'asc' ? ArrowUp : ArrowDown;
}

function openCreate()        { editingCategory.value = null; showFormModal.value = true; }
function openEdit(cat)       { editingCategory.value = cat; showFormModal.value = true; }
function closeFormModal()    { showFormModal.value = false; editingCategory.value = null; }
function openDelete(cat)     { deletingCategory.value = cat; }

async function saveCategory(data) {
  try {
    if (editingCategory.value) {
      const updated = await categoryApi.update(editingCategory.value.id, data);
      const idx = categories.value.findIndex(c => c.id === editingCategory.value.id);
      if (idx !== -1) categories.value[idx] = updated;
    } else {
      const created = await categoryApi.create(data);
      categories.value.push(created);
    }
    closeFormModal();
  } catch (e) {
    alert(e?.errors ? Object.values(e.errors).flat().join('\n') : 'Save failed');
  }
}

async function confirmDelete() {
  try {
    await categoryApi.remove(deletingCategory.value.id);
    categories.value = categories.value.filter(c => c.id !== deletingCategory.value.id);
    deletingCategory.value = null;
  } catch { alert('Delete failed'); }
}
</script>
