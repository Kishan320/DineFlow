<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Waiters List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Waiters</p>
      </div>
      <button
        @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)"
      >
        <PlusIcon :size="15" /> Add Waiter
      </button>
    </div>

    <!-- Waiters List -->
    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Waiters List</h2>
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
              <td :colspan="columns.length + 1" class="px-4 py-12 text-center text-sm" style="color:var(--muted-foreground)">No waiter records found</td>
            </tr>
            <tr
              v-for="(waiter, idx) in paginated"
              :key="waiter.id"
              class="border-b last:border-0 transition-colors hover:bg-muted/40"
              style="border-color:var(--border)"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs font-semibold" style="color:var(--foreground)">{{ waiter.waiterCode }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--foreground)">{{ waiter.name }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs tabular-nums" style="color:var(--foreground)">{{ waiter.dob }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs tabular-nums" style="color:var(--foreground)">{{ waiter.mobile }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <button @click="openEdit(waiter)" class="p-1.5 rounded-lg hover:bg-info/10 transition-colors" style="color:var(--primary)" title="Edit">
                    <EditIcon :size="13" />
                  </button>
                  <button @click="openDelete(waiter)" class="p-1.5 rounded-lg hover:bg-danger/10 transition-colors" style="color:var(--danger)" title="Delete">
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
    <WaiterFormModal
      v-if="showFormModal"
      :waiter="editingWaiter"
      @close="closeFormModal"
      @save="saveWaiter"
    />
    <WaiterDeleteModal
      v-if="deletingWaiter"
      :waiter="deletingWaiter"
      @close="deletingWaiter = null"
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
import WaiterFormModal   from './WaiterFormModal.vue';
import WaiterDeleteModal from './WaiterDeleteModal.vue';

const search   = ref('');
const sortKey  = ref('waiterCode');
const sortDir  = ref('asc');
const page     = ref(1);
const pageSize = ref(10);

const showFormModal  = ref(false);
const editingWaiter  = ref(null);
const deletingWaiter = ref(null);

const waiters = ref([
  { id: 1,  waiterCode: 'W1',  name: 'Ram',      dob: '19-06-1998', mobile: '9876543210' },
  { id: 2,  waiterCode: 'W2',  name: 'Priya',    dob: '05-03-1995', mobile: '9823456781' },
  { id: 3,  waiterCode: 'W3',  name: 'Arjun',    dob: '22-11-1997', mobile: '9712345678' },
  { id: 4,  waiterCode: 'W4',  name: 'Sneha',    dob: '14-07-2000', mobile: '9654321098' },
  { id: 5,  waiterCode: 'W5',  name: 'Karthik',  dob: '30-01-1993', mobile: '9543210987' },
  { id: 6,  waiterCode: 'W6',  name: 'Divya',    dob: '08-09-1999', mobile: '9432109876' },
  { id: 7,  waiterCode: 'W7',  name: 'Suresh',   dob: '17-04-1996', mobile: '9321098765' },
  { id: 8,  waiterCode: 'W8',  name: 'Meena',    dob: '25-12-2001', mobile: '9210987654' },
  { id: 9,  waiterCode: 'W9',  name: 'Vijay',    dob: '11-02-1994', mobile: '9109876543' },
  { id: 10, waiterCode: 'W10', name: 'Lakshmi',  dob: '03-08-1998', mobile: '9098765432' },
]);

const columns = [
  { key: 'waiterCode', label: 'Waiter Code' },
  { key: 'name',       label: 'Name' },
  { key: 'dob',        label: 'DOB' },
  { key: 'mobile',     label: 'Mobile' },
];

const filtered = computed(() => {
  let data = waiters.value.filter(w =>
    !search.value ||
    w.waiterCode.toLowerCase().includes(search.value.toLowerCase()) ||
    w.name.toLowerCase().includes(search.value.toLowerCase()) ||
    w.mobile.includes(search.value)
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

function openCreate()      { editingWaiter.value = null; showFormModal.value = true; }
function openEdit(waiter)  { editingWaiter.value = waiter; showFormModal.value = true; }
function closeFormModal()  { showFormModal.value = false; editingWaiter.value = null; }
function openDelete(waiter){ deletingWaiter.value = waiter; }

function saveWaiter(data) {
  if (editingWaiter.value) {
    const idx = waiters.value.findIndex(w => w.id === editingWaiter.value.id);
    if (idx !== -1) waiters.value[idx] = { ...waiters.value[idx], ...data };
  } else {
    const nextCode = 'W' + (waiters.value.length + 1);
    waiters.value.push({ id: Date.now(), waiterCode: data.waiterCode || nextCode, ...data });
  }
  closeFormModal();
}

function confirmDelete() {
  waiters.value = waiters.value.filter(w => w.id !== deletingWaiter.value.id);
  deletingWaiter.value = null;
}
</script>
