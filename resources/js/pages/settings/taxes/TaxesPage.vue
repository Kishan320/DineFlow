<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Tax List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Taxes</p>
      </div>
      <button @click="openCreate" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">
        <PlusIcon :size="15" /> Add Tax
      </button>
    </div>

    <!-- Taxes List -->
    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Taxes List</h2>
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
              <th v-for="col in columns" :key="col.key" @click="toggleSort(col.key)" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer select-none" style="color:var(--muted-foreground)">
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
              <td :colspan="columns.length + 1" class="px-4 py-12 text-center text-sm" style="color:var(--muted-foreground)">No tax records found</td>
            </tr>
            <tr
              v-for="(tax, idx) in paginated"
              :key="tax.id"
              class="border-b last:border-0 transition-colors hover:bg-muted/40"
              style="border-color:var(--border)"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs font-semibold" style="color:var(--foreground)">{{ tax.hsnCode }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--foreground)">{{ tax.description }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs tabular-nums" style="color:var(--foreground)">{{ tax.taxPercent.toFixed(2) }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-xs font-medium" style="color:var(--foreground)">{{ tax.lastAccessedBy }}</span>
                  <span class="text-xs" style="color:var(--muted-foreground)">{{ tax.lastAccessedAt }}</span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <button @click="openEdit(tax)" class="p-1.5 rounded-lg hover:bg-info/10 transition-colors" style="color:var(--primary)" title="Edit">
                    <EditIcon :size="13" />
                  </button>
                  <button @click="openDelete(tax)" class="p-1.5 rounded-lg hover:bg-danger/10 transition-colors" style="color:var(--danger)" title="Delete">
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
    <TaxFormModal
      v-if="showFormModal"
      :tax="editingTax"
      @close="closeFormModal"
      @save="saveTax"
    />
    <TaxDeleteModal
      v-if="deletingTax"
      :tax="deletingTax"
      @close="deletingTax = null"
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
import TaxFormModal   from './TaxFormModal.vue';
import TaxDeleteModal from './TaxDeleteModal.vue';

const search   = ref('');
const sortKey  = ref('hsnCode');
const sortDir  = ref('asc');
const page     = ref(1);
const pageSize = ref(10);

const showFormModal = ref(false);
const editingTax    = ref(null);
const deletingTax   = ref(null);

const taxes = ref([
  { id: 1, hsnCode: 'GST',    description: 'GST 5% (B)',      cgst: 2.5, sgst: 2.5, igst: 0, cess: 0, additionalCess: 0, vat: 0, taxPercent: 5.00,  lastAccessedBy: 'Administrator', lastAccessedAt: '24-May-2021 10:56 am' },
  { id: 2, hsnCode: 'GST12',  description: 'GST 12%',         cgst: 6,   sgst: 6,   igst: 0, cess: 0, additionalCess: 0, vat: 0, taxPercent: 12.00, lastAccessedBy: 'Administrator', lastAccessedAt: '01-Jun-2021 09:30 am' },
  { id: 3, hsnCode: 'GST18',  description: 'GST 18%',         cgst: 9,   sgst: 9,   igst: 0, cess: 0, additionalCess: 0, vat: 0, taxPercent: 18.00, lastAccessedBy: 'Administrator', lastAccessedAt: '15-Jun-2021 11:00 am' },
  { id: 4, hsnCode: 'GST28',  description: 'GST 28%',         cgst: 14,  sgst: 14,  igst: 0, cess: 0, additionalCess: 0, vat: 0, taxPercent: 28.00, lastAccessedBy: 'Manager',       lastAccessedAt: '20-Jul-2021 02:15 pm' },
  { id: 5, hsnCode: 'VAT5',   description: 'VAT 5%',          cgst: 0,   sgst: 0,   igst: 0, cess: 0, additionalCess: 0, vat: 5, taxPercent: 5.00,  lastAccessedBy: 'Administrator', lastAccessedAt: '10-Aug-2021 08:45 am' },
  { id: 6, hsnCode: 'EXEMPT', description: 'Tax Exempt (0%)', cgst: 0,   sgst: 0,   igst: 0, cess: 0, additionalCess: 0, vat: 0, taxPercent: 0.00,  lastAccessedBy: 'Administrator', lastAccessedAt: '05-Sep-2021 04:00 pm' },
]);

const columns = [
  { key: 'hsnCode',        label: 'HSN Code' },
  { key: 'description',    label: 'Description' },
  { key: 'taxPercent',     label: 'Tax (%)' },
  { key: 'lastAccessedBy', label: 'Last Accessed By' },
];

const filtered = computed(() => {
  let data = taxes.value.filter(t =>
    !search.value ||
    t.hsnCode.toLowerCase().includes(search.value.toLowerCase()) ||
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

function openCreate()    { editingTax.value = null; showFormModal.value = true; }
function openEdit(tax)   { editingTax.value = tax;  showFormModal.value = true; }
function closeFormModal(){ showFormModal.value = false; editingTax.value = null; }
function openDelete(tax) { deletingTax.value = tax; }

function saveTax(data) {
  const now = new Date().toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
  if (editingTax.value) {
    const idx = taxes.value.findIndex(t => t.id === editingTax.value.id);
    if (idx !== -1) taxes.value[idx] = { ...taxes.value[idx], ...data, lastAccessedBy: 'Administrator', lastAccessedAt: now };
  } else {
    taxes.value.push({ id: Date.now(), ...data, lastAccessedBy: 'Administrator', lastAccessedAt: now });
  }
  closeFormModal();
}

function confirmDelete() {
  taxes.value = taxes.value.filter(t => t.id !== deletingTax.value.id);
  deletingTax.value = null;
}
</script>
