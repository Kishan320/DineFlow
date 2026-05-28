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
                <span class="text-xs font-medium" style="color:var(--foreground)">{{ cat.categoryName }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--muted-foreground)">{{ cat.description }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-xs font-medium" style="color:var(--foreground)">{{ cat.lastAccessedBy }}</span>
                  <span class="text-xs" style="color:var(--muted-foreground)">{{ cat.lastAccessedAt }}</span>
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
import { ref, computed } from 'vue';
import {
  Search as SearchIcon, X as XIcon, Plus as PlusIcon,
  Edit as EditIcon, Trash2 as Trash2Icon,
  ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon,
  ArrowUpDown, ArrowUp, ArrowDown,
} from '@lucide/vue';
import CategoryFormModal   from './CategoryFormModal.vue';
import CategoryDeleteModal from './CategoryDeleteModal.vue';

const search   = ref('');
const sortKey  = ref('categoryName');
const sortDir  = ref('asc');
const page     = ref(1);
const pageSize = ref(25);

const showFormModal     = ref(false);
const editingCategory   = ref(null);
const deletingCategory  = ref(null);

const AT = '01-May-2020 05:18 pm';

const categories = ref([
  { id: 1,  categoryName: 'Ayyan Appetizers',   description: 'Starter appetizers',         lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 2,  categoryName: 'Banquet',             description: 'Banquet special items',       lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 3,  categoryName: 'Breads at its best',  description: 'Variety of breads',           lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 4,  categoryName: 'Break Fast',          description: 'Morning breakfast items',     lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 5,  categoryName: 'Break Fast Combo',    description: 'Breakfast combo meals',       lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 6,  categoryName: 'Briyani Chicken',     description: 'Chicken biryani varieties',   lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 7,  categoryName: 'Briyani Egg',         description: 'Egg biryani varieties',       lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 8,  categoryName: 'Briyani Mutton',      description: 'Mutton biryani varieties',    lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 9,  categoryName: 'Briyani Veg',         description: 'Vegetarian biryani',          lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 10, categoryName: 'Burgers',             description: 'Burger varieties',            lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 11, categoryName: 'Chaat Items',         description: 'Indian street chaat',         lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 12, categoryName: 'Chinese',             description: 'Chinese cuisine items',       lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 13, categoryName: 'Cold Beverages',      description: 'Cold drinks and juices',      lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 14, categoryName: 'Curries',             description: 'Gravy and curry dishes',      lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 15, categoryName: 'Desserts',            description: 'Sweet desserts',              lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 16, categoryName: 'Dosa',                description: 'Dosa varieties',              lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 17, categoryName: 'Drinks',              description: 'Beverages and drinks',        lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 18, categoryName: 'Egg Items',           description: 'Egg based dishes',            lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 19, categoryName: 'Fish Items',          description: 'Fish and seafood',            lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 20, categoryName: 'Fried Rice',          description: 'Fried rice varieties',        lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 21, categoryName: 'Grills',              description: 'Grilled items',               lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 22, categoryName: 'Hot Beverages',       description: 'Tea, coffee and hot drinks',  lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 23, categoryName: 'Ice Creams',          description: 'Ice cream varieties',         lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 24, categoryName: 'Idly',                description: 'Idly varieties',              lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 25, categoryName: 'Juices',              description: 'Fresh fruit juices',          lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 26, categoryName: 'Kebabs',              description: 'Kebab varieties',             lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 27, categoryName: 'Meals',               description: 'Full meal combos',            lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 28, categoryName: 'Noodles',             description: 'Noodle dishes',               lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 29, categoryName: 'Parathas',            description: 'Paratha varieties',           lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 30, categoryName: 'Pasta',               description: 'Pasta dishes',                lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 31, categoryName: 'Pizza',               description: 'Pizza varieties',             lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 32, categoryName: 'Rolls',               description: 'Kathi rolls and wraps',       lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 33, categoryName: 'Salads',              description: 'Fresh salads',                lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 34, categoryName: 'Sandwiches',          description: 'Sandwich varieties',          lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 35, categoryName: 'Soups',               description: 'Soup varieties',              lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 36, categoryName: 'Starters',            description: 'Starter dishes',              lastAccessedBy: 'Administrator', lastAccessedAt: AT },
  { id: 37, categoryName: 'Tandoor Items',       description: 'Tandoor cooked items',        lastAccessedBy: 'Administrator', lastAccessedAt: AT },
]);

const columns = [
  { key: 'categoryName',   label: 'Category Name' },
  { key: 'description',    label: 'Description' },
  { key: 'lastAccessedBy', label: 'Last Accessed By' },
];

const filtered = computed(() => {
  let data = categories.value.filter(c =>
    !search.value ||
    c.categoryName.toLowerCase().includes(search.value.toLowerCase()) ||
    c.description.toLowerCase().includes(search.value.toLowerCase())
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

function saveCategory(data) {
  const now = new Date().toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
  if (editingCategory.value) {
    const idx = categories.value.findIndex(c => c.id === editingCategory.value.id);
    if (idx !== -1) categories.value[idx] = { ...categories.value[idx], ...data, lastAccessedBy: 'Administrator', lastAccessedAt: now };
  } else {
    categories.value.push({ id: Date.now(), ...data, lastAccessedBy: 'Administrator', lastAccessedAt: now });
  }
  closeFormModal();
}

function confirmDelete() {
  categories.value = categories.value.filter(c => c.id !== deletingCategory.value.id);
  deletingCategory.value = null;
}
</script>
