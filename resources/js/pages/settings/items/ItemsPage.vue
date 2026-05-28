<template>
  <!-- Form view -->
  <ItemFormPage
    v-if="view !== 'list'"
    :item="editingItem"
    @back="closeForm"
    @save="saveItem"
  />

  <!-- List view -->
  <div v-else class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Item List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Items</p>
      </div>
      <button
        @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)"
      >
        <PlusIcon :size="15" /> Add Item
      </button>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Item List</h2>
      </div>

      <!-- Controls -->
      <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b" style="border-color:var(--border)">
        <select v-model.number="pageSize" @change="page = 1" class="border rounded-lg px-2 py-1.5 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option v-for="n in [10, 25, 50]" :key="n" :value="n">{{ n }}</option>
        </select>

        <!-- Search -->
        <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 flex-1 min-w-[160px] max-w-xs" style="background:var(--muted)">
          <SearchIcon :size="13" style="color:var(--muted-foreground)" />
          <input v-model="search" @input="page = 1" type="text" placeholder="Type to Search..." class="flex-1 bg-transparent text-xs outline-none" style="color:var(--foreground)" />
          <button v-if="search" @click="search = ''" style="color:var(--muted-foreground)"><XIcon :size="12" /></button>
        </div>

        <!-- Category filter -->
        <select v-model="filterCategory" @change="page = 1" class="border rounded-lg px-2 py-1.5 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option value="">All Categories</option>
          <option v-for="c in categoryOptions" :key="c" :value="c">{{ c }}</option>
        </select>

        <!-- State filter -->
        <select v-model="filterState" @change="page = 1" class="border rounded-lg px-2 py-1.5 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option value="">All States</option>
          <option value="On Sale">On Sale</option>
          <option value="Off Sale">Off Sale</option>
        </select>

        <!-- Type filter -->
        <select v-model="filterType" @change="page = 1" class="border rounded-lg px-2 py-1.5 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option value="">All Types</option>
          <option value="Physical Item">Physical Item</option>
          <option value="Digital Item">Digital Item</option>
          <option value="Service">Service</option>
        </select>
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
              <td :colspan="columns.length + 1" class="px-4 py-12 text-center text-sm" style="color:var(--muted-foreground)">No item records found</td>
            </tr>
            <tr
              v-for="item in paginated" :key="item.id"
              class="border-b last:border-0 transition-colors hover:bg-muted/40"
              style="border-color:var(--border)"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs font-semibold" style="color:var(--primary)">{{ item.code }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <button @click="viewingItem = item" class="text-xs font-medium hover:underline text-left" style="color:var(--foreground)">{{ item.itemName }}</button>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--muted-foreground)">{{ item.category || '—' }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--foreground)">Rs.{{ item.restaurantPrice.toFixed(2) }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--foreground)">Rs.{{ item.barPrice.toFixed(2) }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--foreground)">Rs.{{ item.roomPrice.toFixed(2) }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                  :style="item.state === 'On Sale'
                    ? 'background:color-mix(in srgb,var(--success,#22c55e) 12%,transparent);color:var(--success,#16a34a)'
                    : 'background:color-mix(in srgb,var(--danger) 12%,transparent);color:var(--danger)'"
                >{{ item.state }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--muted-foreground)">{{ item.itemType }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-xs font-medium" style="color:var(--foreground)">{{ item.lastAccessedBy }}</span>
                  <span class="text-xs" style="color:var(--muted-foreground)">{{ item.lastAccessedAt }}</span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <button @click="openEdit(item)" class="p-1.5 rounded-lg hover:bg-info/10 transition-colors" style="color:var(--primary)" title="Edit">
                    <EditIcon :size="13" />
                  </button>
                  <button @click="deletingItem = item" class="p-1.5 rounded-lg hover:bg-danger/10 transition-colors" style="color:var(--danger)" title="Delete">
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
    <ItemDetailModal v-if="viewingItem" :item="viewingItem" @close="viewingItem = null" />
    <ItemDeleteModal v-if="deletingItem" :item="deletingItem" @close="deletingItem = null" @confirm="confirmDelete" />
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
import ItemFormPage    from './ItemFormPage.vue';
import ItemDetailModal from './ItemDetailModal.vue';
import ItemDeleteModal from './ItemDeleteModal.vue';

const view         = ref('list'); // 'list' | 'form'
const editingItem  = ref(null);
const viewingItem  = ref(null);
const deletingItem = ref(null);

const search         = ref('');
const filterCategory = ref('');
const filterState    = ref('');
const filterType     = ref('');
const sortKey        = ref('itemName');
const sortDir        = ref('asc');
const page           = ref(1);
const pageSize       = ref(25);

const categoryOptions = [
  'New Menu June', 'Break Fast Combo', 'Hot Beverage', 'Briyani Chicken',
  'Briyani Egg', 'Briyani Mutton', 'Briyani Veg', 'Dosa', 'Starters',
  'Curries', 'Breads at its best', 'Desserts', 'Cold Beverages', 'Meals',
  'Ayyan Appetizers', 'Banquet', 'Break Fast', 'Chaat Items', 'Chinese',
];

const AT = '01-May-2020 05:18 pm';

const items = ref([
  { id: 1,  code: '001', itemName: 'Masala Dosa',        category: 'Dosa',            restaurantPrice: 80,  barPrice: 80,  roomPrice: 90,  taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 2,  code: '002', itemName: 'Plain Dosa',          category: 'Dosa',            restaurantPrice: 60,  barPrice: 60,  roomPrice: 70,  taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 3,  code: '003', itemName: 'Chicken Biryani',     category: 'Briyani Chicken', restaurantPrice: 180, barPrice: 190, roomPrice: 200, taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 4,  code: '004', itemName: 'Mutton Biryani',      category: 'Briyani Mutton',  restaurantPrice: 220, barPrice: 230, roomPrice: 250, taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 5,  code: '005', itemName: 'Veg Biryani',         category: 'Briyani Veg',     restaurantPrice: 140, barPrice: 140, roomPrice: 160, taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 6,  code: '006', itemName: 'Paneer Butter Masala',category: 'Curries',         restaurantPrice: 160, barPrice: 160, roomPrice: 180, taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 7,  code: '007', itemName: 'Butter Naan',         category: 'Breads at its best', restaurantPrice: 40, barPrice: 40, roomPrice: 50, taxType: 'Exclusive', tax: 'GST (5.00%)', state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 8,  code: '008', itemName: 'Cold Coffee',         category: 'Cold Beverages',  restaurantPrice: 80,  barPrice: 90,  roomPrice: 100, taxType: 'Inclusive', tax: 'GST (12.00%)', state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 9,  code: '009', itemName: 'Masala Chai',         category: 'Hot Beverage',    restaurantPrice: 30,  barPrice: 30,  roomPrice: 40,  taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 10, code: '010', itemName: 'Gulab Jamun',         category: 'Desserts',        restaurantPrice: 60,  barPrice: 60,  roomPrice: 70,  taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'Off Sale', itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 11, code: '011', itemName: 'Veg Fried Rice',      category: 'Meals',           restaurantPrice: 120, barPrice: 120, roomPrice: 140, taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 12, code: '012', itemName: 'Chicken 65',          category: 'Starters',        restaurantPrice: 200, barPrice: 210, roomPrice: 220, taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 13, code: '013', itemName: 'Idly (2 pcs)',        category: 'Break Fast',      restaurantPrice: 40,  barPrice: 40,  roomPrice: 50,  taxType: 'Exclusive', tax: 'Tax Exempt',   state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 14, code: '014', itemName: 'Vada (2 pcs)',        category: 'Break Fast',      restaurantPrice: 40,  barPrice: 40,  roomPrice: 50,  taxType: 'Exclusive', tax: 'Tax Exempt',   state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
  { id: 15, code: '015', itemName: 'Puri Bhaji',          category: 'Break Fast',      restaurantPrice: 60,  barPrice: 60,  roomPrice: 70,  taxType: 'Exclusive', tax: 'GST (5.00%)',  state: 'On Sale',  itemType: 'Physical Item', note: '', imageUrl: '', lastAccessedBy: 'Administrator', lastAccessedAt: AT, createdAt: AT },
]);

const columns = [
  { key: 'code',            label: 'Item Code' },
  { key: 'itemName',        label: 'Item Name' },
  { key: 'category',        label: 'Category' },
  { key: 'restaurantPrice', label: 'Rest. Price' },
  { key: 'barPrice',        label: 'Bar Price' },
  { key: 'roomPrice',       label: 'Room Price' },
  { key: 'state',           label: 'State' },
  { key: 'itemType',        label: 'Type' },
  { key: 'lastAccessedBy',  label: 'Last Accessed By' },
];

const filtered = computed(() => {
  let data = items.value.filter(i => {
    const q = search.value.toLowerCase();
    const matchSearch = !q || i.itemName.toLowerCase().includes(q) || i.code.toLowerCase().includes(q) || i.category.toLowerCase().includes(q);
    const matchCat    = !filterCategory.value || i.category === filterCategory.value;
    const matchState  = !filterState.value    || i.state    === filterState.value;
    const matchType   = !filterType.value     || i.itemType === filterType.value;
    return matchSearch && matchCat && matchState && matchType;
  });
  if (sortKey.value) {
    data = [...data].sort((a, b) => {
      const av = a[sortKey.value], bv = b[sortKey.value];
      const cmp = typeof av === 'number' ? av - bv : String(av).localeCompare(String(bv));
      return sortDir.value === 'asc' ? cmp : -cmp;
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

function openCreate()  { editingItem.value = null; view.value = 'form'; }
function openEdit(i)   { editingItem.value = i;    view.value = 'form'; }
function closeForm()   { view.value = 'list'; editingItem.value = null; }

function saveItem(data) {
  const now = new Date().toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
  if (editingItem.value) {
    const idx = items.value.findIndex(i => i.id === editingItem.value.id);
    if (idx !== -1) items.value[idx] = { ...items.value[idx], ...data, lastAccessedBy: 'Administrator', lastAccessedAt: now };
  } else {
    items.value.push({ id: Date.now(), ...data, lastAccessedBy: 'Administrator', lastAccessedAt: now, createdAt: now });
  }
  closeForm();
}

function confirmDelete() {
  items.value = items.value.filter(i => i.id !== deletingItem.value.id);
  deletingItem.value = null;
}
</script>
