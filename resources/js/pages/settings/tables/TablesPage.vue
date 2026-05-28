<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Tables List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Tables</p>
      </div>
      <button
        @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)"
      >
        <PlusIcon :size="15" /> Add Table
      </button>
    </div>

    <!-- Tables List -->
    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Restaurant Tables List</h2>
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
              <td :colspan="columns.length + 1" class="px-4 py-12 text-center text-sm" style="color:var(--muted-foreground)">No table records found</td>
            </tr>
            <tr
              v-for="(table, idx) in paginated"
              :key="table.id"
              class="border-b last:border-0 transition-colors hover:bg-muted/40"
              style="border-color:var(--border)"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs font-semibold" style="color:var(--foreground)">{{ table.tableName }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--muted-foreground)">{{ table.description }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs tabular-nums font-medium" style="color:var(--foreground)">{{ table.maxSeats }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-xs font-medium" style="color:var(--foreground)">{{ table.lastAccessedBy }}</span>
                  <span class="text-xs" style="color:var(--muted-foreground)">{{ table.lastAccessedAt }}</span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <button @click="openEdit(table)" class="p-1.5 rounded-lg hover:bg-info/10 transition-colors" style="color:var(--primary)" title="Edit">
                    <EditIcon :size="13" />
                  </button>
                  <button @click="openDelete(table)" class="p-1.5 rounded-lg hover:bg-danger/10 transition-colors" style="color:var(--danger)" title="Delete">
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
    <TableFormModal
      v-if="showFormModal"
      :table="editingTable"
      @close="closeFormModal"
      @save="saveTable"
    />
    <TableDeleteModal
      v-if="deletingTable"
      :table="deletingTable"
      @close="deletingTable = null"
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
import TableFormModal   from './TableFormModal.vue';
import TableDeleteModal from './TableDeleteModal.vue';

const search   = ref('');
const sortKey  = ref('tableName');
const sortDir  = ref('asc');
const page     = ref(1);
const pageSize = ref(25);

const showFormModal  = ref(false);
const editingTable   = ref(null);
const deletingTable  = ref(null);

const tables = ref([
  { id: 1,  tableName: '9A',   description: 'Ground Floor',  maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '20-Nov-2020 03:54 pm' },
  { id: 2,  tableName: 'T-5A', description: 'Ground Floor',  maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '09-Nov-2020 02:14 pm' },
  { id: 3,  tableName: 'T-12A',description: 'First Floor',   maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '11-Nov-2020 03:12 pm' },
  { id: 4,  tableName: 'T-14A',description: 'First Floor',   maxSeats: 8, lastAccessedBy: 'Selvakannan', lastAccessedAt: '07-Nov-2020 02:17 pm' },
  { id: 5,  tableName: 'T-1A', description: 'Ground Floor',  maxSeats: 2, lastAccessedBy: 'Selvakannan', lastAccessedAt: '08-Nov-2020 01:56 pm' },
  { id: 6,  tableName: 'T-20', description: 'Terrace',       maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '03-Jan-2021 01:46 pm' },
  { id: 7,  tableName: 'T-24', description: 'Terrace',       maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '05-Jan-2021 11:20 am' },
  { id: 8,  tableName: 'T-2',  description: 'Ground Floor',  maxSeats: 2, lastAccessedBy: 'Selvakannan', lastAccessedAt: '12-Nov-2020 10:30 am' },
  { id: 9,  tableName: 'T-3',  description: 'Ground Floor',  maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '13-Nov-2020 09:15 am' },
  { id: 10, tableName: 'T-4',  description: 'Ground Floor',  maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '14-Nov-2020 08:45 am' },
  { id: 11, tableName: 'T-6',  description: 'First Floor',   maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '15-Nov-2020 02:00 pm' },
  { id: 12, tableName: 'T-7',  description: 'First Floor',   maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '16-Nov-2020 03:30 pm' },
  { id: 13, tableName: 'T-8',  description: 'First Floor',   maxSeats: 8, lastAccessedBy: 'Selvakannan', lastAccessedAt: '17-Nov-2020 04:00 pm' },
  { id: 14, tableName: 'T-9',  description: 'First Floor',   maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '18-Nov-2020 01:15 pm' },
  { id: 15, tableName: 'T-10', description: 'First Floor',   maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '19-Nov-2020 11:00 am' },
  { id: 16, tableName: 'T-11', description: 'Second Floor',  maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '21-Nov-2020 10:00 am' },
  { id: 17, tableName: 'T-13', description: 'Second Floor',  maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '22-Nov-2020 09:30 am' },
  { id: 18, tableName: 'T-15', description: 'Second Floor',  maxSeats: 8, lastAccessedBy: 'Selvakannan', lastAccessedAt: '23-Nov-2020 02:45 pm' },
  { id: 19, tableName: 'T-16', description: 'Second Floor',  maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '24-Nov-2020 03:00 pm' },
  { id: 20, tableName: 'T-17', description: 'Terrace',       maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '25-Nov-2020 04:15 pm' },
  { id: 21, tableName: 'T-18', description: 'Terrace',       maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '26-Nov-2020 01:30 pm' },
  { id: 22, tableName: 'T-19', description: 'Terrace',       maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '27-Nov-2020 11:45 am' },
  { id: 23, tableName: 'T-21', description: 'Terrace',       maxSeats: 8, lastAccessedBy: 'Selvakannan', lastAccessedAt: '28-Nov-2020 10:15 am' },
  { id: 24, tableName: 'T-22', description: 'Terrace',       maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '29-Nov-2020 09:00 am' },
  { id: 25, tableName: 'T-23', description: 'Terrace',       maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '30-Nov-2020 08:30 am' },
  { id: 26, tableName: 'T-25', description: 'Private Room',  maxSeats: 10,lastAccessedBy: 'Selvakannan', lastAccessedAt: '01-Dec-2020 02:00 pm' },
  { id: 27, tableName: 'T-26', description: 'Private Room',  maxSeats: 10,lastAccessedBy: 'Selvakannan', lastAccessedAt: '02-Dec-2020 03:30 pm' },
  { id: 28, tableName: 'T-27', description: 'Private Room',  maxSeats: 8, lastAccessedBy: 'Selvakannan', lastAccessedAt: '03-Dec-2020 04:00 pm' },
  { id: 29, tableName: 'T-28', description: 'Outdoor',       maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '04-Dec-2020 01:00 pm' },
  { id: 30, tableName: 'T-29', description: 'Outdoor',       maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '05-Dec-2020 11:30 am' },
  { id: 31, tableName: 'T-30', description: 'Outdoor',       maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '06-Dec-2020 10:00 am' },
  { id: 32, tableName: 'T-31', description: 'Outdoor',       maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '07-Dec-2020 09:15 am' },
  { id: 33, tableName: 'T-32', description: 'Bar Area',      maxSeats: 2, lastAccessedBy: 'Selvakannan', lastAccessedAt: '08-Dec-2020 08:45 am' },
  { id: 34, tableName: 'T-33', description: 'Bar Area',      maxSeats: 2, lastAccessedBy: 'Selvakannan', lastAccessedAt: '09-Dec-2020 02:30 pm' },
  { id: 35, tableName: 'T-34', description: 'Bar Area',      maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '10-Dec-2020 03:45 pm' },
  { id: 36, tableName: 'T-35', description: 'Bar Area',      maxSeats: 4, lastAccessedBy: 'Selvakannan', lastAccessedAt: '11-Dec-2020 04:30 pm' },
  { id: 37, tableName: 'T-36', description: 'Bar Area',      maxSeats: 6, lastAccessedBy: 'Selvakannan', lastAccessedAt: '12-Dec-2020 01:15 pm' },
]);

const columns = [
  { key: 'tableName',      label: 'Table Name' },
  { key: 'description',    label: 'Description' },
  { key: 'maxSeats',       label: 'Max Seats' },
  { key: 'lastAccessedBy', label: 'Last Accessed By' },
];

const filtered = computed(() => {
  let data = tables.value.filter(t =>
    !search.value ||
    t.tableName.toLowerCase().includes(search.value.toLowerCase()) ||
    t.description.toLowerCase().includes(search.value.toLowerCase())
  );
  if (sortKey.value) {
    data = [...data].sort((a, b) => {
      const av = a[sortKey.value], bv = b[sortKey.value];
      if (typeof av === 'number') return sortDir.value === 'asc' ? av - bv : bv - av;
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

function openCreate()      { editingTable.value = null; showFormModal.value = true; }
function openEdit(table)   { editingTable.value = table; showFormModal.value = true; }
function closeFormModal()  { showFormModal.value = false; editingTable.value = null; }
function openDelete(table) { deletingTable.value = table; }

function saveTable(data) {
  const now = new Date().toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
  if (editingTable.value) {
    const idx = tables.value.findIndex(t => t.id === editingTable.value.id);
    if (idx !== -1) tables.value[idx] = { ...tables.value[idx], ...data, lastAccessedBy: 'Selvakannan', lastAccessedAt: now };
  } else {
    tables.value.push({ id: Date.now(), ...data, lastAccessedBy: 'Selvakannan', lastAccessedAt: now });
  }
  closeFormModal();
}

function confirmDelete() {
  tables.value = tables.value.filter(t => t.id !== deletingTable.value.id);
  deletingTable.value = null;
}
</script>
