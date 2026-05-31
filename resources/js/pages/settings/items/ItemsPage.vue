<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Item List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Items</p>
      </div>
      <button @click="router.push({ name: 'settings-items-create' })"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)">
        <PlusIcon :size="15" /> Add Item
      </button>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Item List</h2>
      </div>

      <!-- Controls -->
      <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b" style="border-color:var(--border)">
        <select v-model="perPage" @change="goToPage(1)"
          class="border rounded-lg px-2 py-1.5 text-xs outline-none"
          style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
        </select>
        <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 flex-1 min-w-[160px] max-w-xs" style="background:var(--muted)">
          <SearchIcon :size="13" style="color:var(--muted-foreground)" />
          <input v-model="search" @input="onSearch" type="text" placeholder="Type to Search..."
            class="flex-1 bg-transparent text-xs outline-none" style="color:var(--foreground)" />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-sm" style="border-collapse:collapse">
          <thead>
            <tr class="border-b" style="border-color:var(--border);background:color-mix(in srgb, var(--muted) 30%, transparent)">
              <th class="th">#</th>
              <th class="th sortable" @click="setSort('code')">Item Code <SortIcon col="code" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('item_name')">Item Name <SortIcon col="item_name" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('category')">Category <SortIcon col="category" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('restaurant_price')">Rest. Price <SortIcon col="restaurant_price" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('bar_price')">Bar Price <SortIcon col="bar_price" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('room_price')">Room Price <SortIcon col="room_price" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('state')">State <SortIcon col="state" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('item_type')">Type <SortIcon col="item_type" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('last_accessed_by')">Last Accessed By <SortIcon col="last_accessed_by" :sort="sort" :dir="dir" /></th>
              <th class="th sortable" @click="setSort('updated_at')">Updated At <SortIcon col="updated_at" :sort="sort" :dir="dir" /></th>
              <th class="th">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="12" class="td text-center" style="color:var(--muted-foreground)">Loading…</td>
            </tr>
            <tr v-else-if="!rows.length">
              <td colspan="12" class="td text-center" style="color:var(--muted-foreground)">No entries found</td>
            </tr>
            <tr v-for="(row, i) in rows" :key="row.id" class="row">
              <td class="td">{{ (page - 1) * perPage + i + 1 }}</td>
              <td class="td">{{ row.code }}</td>
              <td class="td">{{ row.item_name }}</td>
              <td class="td">{{ row.category || '—' }}</td>
              <td class="td">Rs.{{ parseFloat(row.restaurant_price).toFixed(2) }}</td>
              <td class="td">Rs.{{ parseFloat(row.bar_price).toFixed(2) }}</td>
              <td class="td">Rs.{{ parseFloat(row.room_price).toFixed(2) }}</td>
              <td class="td">
                <span :class="row.state === 'On Sale' ? 'badge-on' : 'badge-off'">{{ row.state }}</span>
              </td>
              <td class="td">{{ row.item_type }}</td>
              <td class="td">{{ row.last_accessed_by }}</td>
              <td class="td">{{ row.updated_at ? new Date(row.updated_at).toLocaleString('en-IN') : '' }}</td>
              <td class="td">
                <div class="flex items-center gap-1">
                  <button @click="viewingItemId = row.id" class="p-1.5 rounded-lg transition-colors" title="View" style="color:var(--muted-foreground)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                  <button @click="router.push({ name: 'settings-items-edit', params: { id: row.id } })" class="p-1.5 rounded-lg transition-colors" title="Edit" style="color:var(--primary)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </button>
                  <button @click="deletingItem = row" class="p-1.5 rounded-lg transition-colors" title="Delete" style="color:var(--danger)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
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
          {{ total === 0 ? 'No entries' : `Showing ${(page - 1) * perPage + 1} to ${Math.min(page * perPage, total)} of ${total} entries` }}
        </span>
        <div class="flex items-center gap-1">
          <button class="pg-btn" @click="goToPage(1)" :disabled="page === 1">«</button>
          <button class="pg-btn" @click="goToPage(page - 1)" :disabled="page === 1">‹</button>
          <button v-for="p in pageNumbers" :key="p" class="pg-btn" :class="{ active: p === page }" @click="goToPage(p)">{{ p }}</button>
          <button class="pg-btn" @click="goToPage(page + 1)" :disabled="page === pages">›</button>
          <button class="pg-btn" @click="goToPage(pages)" :disabled="page === pages">»</button>
        </div>
      </div>
    </div>

    <ItemDetailModal v-if="viewingItemId" :item-id="viewingItemId" @close="viewingItemId = null" />
    <ItemDeleteModal v-if="deletingItem" :item="deletingItem" @close="deletingItem = null" @confirm="confirmDelete" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus as PlusIcon, Search as SearchIcon } from '@lucide/vue';
import ItemDeleteModal from './ItemDeleteModal.vue';
import ItemDetailModal from './ItemDetailModal.vue';
import { itemApi } from '@/services/settingsApi';
import { toast } from 'vue-sonner';

const router = useRouter();

const rows    = ref([]);
const total   = ref(0);
const page    = ref(1);
const pages   = ref(1);
const perPage = ref(25);
const search  = ref('');
const sort    = ref('item_name');
const dir     = ref('asc');
const loading = ref(false);

const deletingItem  = ref(null);
const viewingItemId = ref(null);

let searchTimer;

const pageNumbers = computed(() => {
  const count = Math.min(5, pages.value);
  const start = page.value < 3 ? 1 : page.value >= pages.value - 1 ? Math.max(1, pages.value - 4) : page.value - 2;
  return Array.from({ length: count }, (_, i) => start + i);
});

async function load() {
  loading.value = true;
  try {
    const res = await itemApi.list({ page: page.value, per_page: perPage.value, search: search.value, sort: sort.value, dir: dir.value });
    rows.value  = res.data;
    total.value = res.total;
    pages.value = res.pages;
  } catch {
    toast.error('Failed to load items');
  } finally {
    loading.value = false;
  }
}

function goToPage(p) {
  if (p < 1 || p > pages.value) return;
  page.value = p;
  load();
}

function onSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => goToPage(1), 400);
}

function setSort(col) {
  if (sort.value === col) dir.value = dir.value === 'asc' ? 'desc' : 'asc';
  else { sort.value = col; dir.value = 'asc'; }
  goToPage(1);
}

onMounted(load);

const SortIcon = {
  props: ['col', 'sort', 'dir'],
  template: `<span style="font-size:10px;margin-left:2px;opacity:0.5">
    <template v-if="col === sort">{{ dir === 'asc' ? '▲' : '▼' }}</template>
    <template v-else>⇅</template>
  </span>`,
};

async function confirmDelete() {
  try {
    await itemApi.remove(deletingItem.value.id);
    toast.success('Item deleted successfully!');
    deletingItem.value = null;
    load();
  } catch {
    toast.error('Failed to delete item');
  }
}
</script>

<style scoped>
.th {
  padding: 10px 16px;
  text-align: left;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  white-space: nowrap;
  color: var(--muted-foreground);
}
.th.sortable { cursor: pointer; user-select: none; }
.td {
  padding: 10px 16px;
  font-size: 12px;
  white-space: nowrap;
  color: var(--foreground);
}
.row { border-bottom: 1px solid var(--border); }
.row:hover { background: color-mix(in srgb, var(--muted) 40%, transparent); }
.badge-on {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 500;
  background: color-mix(in srgb, #22c55e 12%, transparent);
  color: #16a34a;
}
.badge-off {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 500;
  background: color-mix(in srgb, var(--danger) 12%, transparent);
  color: var(--danger);
}
.pg-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  background: none;
  color: var(--muted-foreground);
}
.pg-btn:disabled { opacity: 0.35; cursor: default; }
.pg-btn.active { background: var(--primary); color: var(--primary-foreground); }
.pg-btn:not(.active):not(:disabled):hover {
  background: color-mix(in srgb, var(--primary) 15%, transparent);
  color: var(--primary);
}
</style>
