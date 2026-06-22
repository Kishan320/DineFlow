<template>
  <div>
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div
        v-for="card in summaryCards"
        :key="card.id"
        :class="['border rounded-xl p-4 shadow-card', card.alert ? 'border-danger/30' : 'border-border']"
        style="background:var(--card)"
      >
        <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--muted-foreground)">{{ card.label }}</p>
        <p class="text-xl font-bold tabular-nums" :style="card.alert ? 'color:var(--danger)' : 'color:var(--foreground)'">{{ card.value }}</p>
        <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">{{ card.sub }}</p>
      </div>
    </div>

    <!-- Filters + Table -->
    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <!-- Filters bar -->
      <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b" style="border-color:var(--border)">
        <div class="flex items-center gap-2 rounded-lg px-3 py-2 flex-1 min-w-48" style="background:var(--muted)">
          <SearchIcon :size="14" class="flex-shrink-0" style="color:var(--muted-foreground)" />
          <input
            v-model="search"
            @input="onFilterChange"
            type="text"
            placeholder="Search bill no, customer, sales code..."
            class="flex-1 bg-transparent text-sm outline-none"
            style="color:var(--foreground)"
          />
          <button v-if="search" @click="search = ''; onFilterChange()" style="color:var(--muted-foreground)"><XIcon :size="13" /></button>
        </div>

        <!-- Status filter -->
        <div class="flex items-center gap-1.5 flex-wrap">
          <button
            v-for="s in statuses" :key="s"
            @click="statusFilter = s; onFilterChange()"
            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
            :style="statusFilter === s ? 'background:var(--primary);color:var(--primary-foreground)' : 'background:var(--muted);color:var(--muted-foreground)'"
          >{{ s }}</button>
        </div>

        <!-- Bill type filter -->
        <div class="flex items-center gap-1.5 flex-wrap">
          <button
            v-for="t in billTypes" :key="t"
            @click="billTypeFilter = t; onFilterChange()"
            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
            :style="billTypeFilter === t ? 'background:var(--secondary);color:var(--secondary-foreground)' : 'background:var(--muted);color:var(--muted-foreground)'"
          >{{ t }}</button>
        </div>

        <button
          @click="exportCsv"
          class="ml-auto flex items-center gap-1.5 px-3 py-2 rounded-lg border text-xs font-medium hover:bg-muted transition-colors flex-shrink-0"
          style="border-color:var(--border);color:var(--foreground)"
        >
          <DownloadIcon :size="14" /> Export
        </button>
      </div>

      <!-- Bulk action bar -->
      <div v-if="selectedIds.size > 0" class="flex items-center gap-3 px-4 py-2.5 border-b" style="background:color-mix(in srgb, var(--primary) 5%, transparent);border-color:color-mix(in srgb, var(--primary) 20%, transparent)">
        <span class="text-xs font-semibold" style="color:var(--primary)">{{ selectedIds.size }} selected</span>
        <button @click="selectedIds.clear()" class="ml-auto text-xs hover:text-foreground transition-colors" style="color:var(--muted-foreground)">Clear selection</button>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="flex items-center justify-center py-16">
        <div class="spinner" />
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b" style="border-color:var(--border);background:color-mix(in srgb, var(--muted) 30%, transparent)">
              <th class="px-4 py-3 w-10">
                <input type="checkbox" :checked="selectedIds.size === orders.length && orders.length > 0" @change="toggleSelectAll" class="rounded accent-primary" />
              </th>
              <th v-for="col in columns" :key="col.key" @click="toggleSort(col.key)" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer hover:text-foreground transition-colors select-none" style="color:var(--muted-foreground)">
                <div class="flex items-center gap-1.5">
                  {{ col.label }}
                  <component :is="sortIcon(col.key)" :size="12" :style="sortKey === col.key ? 'color:var(--primary)' : 'color:var(--muted-foreground);opacity:0.5'" />
                </div>
              </th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color:var(--muted-foreground)">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="orders.length === 0">
              <td :colspan="columns.length + 2" class="px-4 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <FilterIcon :size="32" class="opacity-40" style="color:var(--muted-foreground)" />
                  <p class="text-sm font-medium" style="color:var(--foreground)">No sales records found</p>
                  <p class="text-xs" style="color:var(--muted-foreground)">Try adjusting your search or filter criteria</p>
                  <button @click="clearFilters" class="text-xs font-medium hover:underline" style="color:var(--primary)">Clear all filters</button>
                </div>
              </td>
            </tr>
            <tr
              v-for="(order, idx) in orders"
              :key="order.id"
              :class="['border-b last:border-0 transition-colors group', selectedIds.has(order.id) ? 'bg-primary/5' : idx % 2 !== 0 ? 'bg-muted/20 hover:bg-muted/40' : 'hover:bg-muted/40']"
              style="border-color:var(--border)"
            >
              <td class="px-4 py-3 w-10">
                <input type="checkbox" :checked="selectedIds.has(order.id)" @change="toggleSelect(order.id)" class="rounded accent-primary" />
              </td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="font-mono text-xs font-semibold" style="color:var(--foreground)">{{ order.billNo }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="font-mono text-xs" style="color:var(--muted-foreground)">{{ order.salesCode }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm" style="color:var(--foreground)">{{ order.customer }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm" style="color:var(--muted-foreground)">{{ order.table }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span :class="['text-xs font-medium px-2 py-0.5 rounded-full', billTypeClass(order.billType)]">{{ order.billType }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm tabular-nums" style="color:var(--foreground)">{{ fmt(order.tax) }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span :class="['text-sm tabular-nums', order.discount > 0 ? 'font-medium' : '']" :style="order.discount > 0 ? 'color:var(--success)' : 'color:var(--muted-foreground)'">
                  {{ order.discount > 0 ? `-${fmt(order.discount)}` : '—' }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm font-semibold tabular-nums" style="color:var(--foreground)">{{ fmt(order.total) }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm font-medium tabular-nums" style="color:var(--success)">{{ fmt(order.paid) }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-sm tabular-nums font-medium" :style="order.balance > 0 ? 'color:var(--danger)' : 'color:var(--muted-foreground)'">
                  {{ order.balance > 0 ? fmt(order.balance) : '—' }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap"><StatusBadge :status="order.status" /></td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-xs font-medium" style="color:var(--foreground)">{{ order.date }}</span>
                  <span class="text-xs" style="color:var(--muted-foreground)">{{ order.time }}</span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-0.5">
                  <button @click="viewOrder = order" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)" title="View"><EyeIcon :size="14" /></button>
                  <button @click="printOrder(order)" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)" title="Print"><PrinterIcon :size="14" /></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t" style="border-color:var(--border)">
        <div class="flex items-center gap-3 text-xs" style="color:var(--muted-foreground)">
          <span>Showing {{ meta.total === 0 ? 0 : (meta.current_page - 1) * meta.per_page + 1 }}–{{ Math.min(meta.current_page * meta.per_page, meta.total) }} of {{ meta.total }} records</span>
          <div class="flex items-center gap-1.5">
            <span>Rows per page:</span>
            <Select filter v-model.number="pageSize" @change="onFilterChange" class="border rounded-lg px-2 py-1 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)" :options="[{label: '{{ n }}', value: '{{ n }}'}]" optionLabel="label" optionValue="value" />
          </div>
        </div>
        <div class="flex items-center gap-1">
          <button @click="goPage(1)" :disabled="meta.current_page === 1" class="px-2 py-1.5 rounded-lg text-xs font-medium hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">First</button>
          <button @click="goPage(meta.current_page - 1)" :disabled="meta.current_page === 1" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)"><ChevronLeftIcon :size="14" /></button>
          <button
            v-for="pn in pageNumbers"
            :key="pn"
            @click="goPage(pn)"
            class="w-8 h-8 rounded-lg text-xs font-medium transition-colors"
            :style="meta.current_page === pn ? 'background:var(--primary);color:var(--primary-foreground)' : 'color:var(--muted-foreground)'"
          >{{ pn }}</button>
          <button @click="goPage(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page || meta.last_page === 0" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)"><ChevronRightIcon :size="14" /></button>
          <button @click="goPage(meta.last_page)" :disabled="meta.current_page === meta.last_page || meta.last_page === 0" class="px-2 py-1.5 rounded-lg text-xs font-medium hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">Last</button>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <SalesDetailModal v-if="viewOrder" :order="viewOrder" @close="viewOrder = null" />
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import {
  Search as SearchIcon, X as XIcon, Download as DownloadIcon,
  Filter as FilterIcon, Eye as EyeIcon, Printer as PrinterIcon,
  ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon,
  ArrowUpDown, ArrowUp, ArrowDown,
} from '@lucide/vue';
import StatusBadge from '@/components/ui/StatusBadge.vue';
import SalesDetailModal from './SalesDetailModal.vue';
import { salesApi } from '@/services/settingsApi.js';

const search        = ref('');
const statusFilter  = ref('All');
const billTypeFilter = ref('All');
const sortKey       = ref('');
const sortDir       = ref('desc');
const pageSize      = ref(10);
const selectedIds   = reactive(new Set());
const viewOrder     = ref(null);
const loading       = ref(false);

const orders  = ref([]);
const summary = ref({ total_bills: 0, total_revenue: 0, total_paid: 0, total_outstanding: 0 });
const meta    = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 });

const statuses  = ['All', 'Pending', 'Confirmed', 'Preparing', 'Ready', 'Served', 'Completed', 'Cancelled'];
const billTypes = ['All', 'Dine In', 'Takeaway', 'Delivery'];

const columns = [
  { key: 'billNo',    label: 'Bill No' },
  { key: 'salesCode', label: 'Sales Code' },
  { key: 'customer',  label: 'Customer' },
  { key: 'table',     label: 'Table / Type' },
  { key: 'billType',  label: 'Bill Type' },
  { key: 'tax',       label: 'Tax' },
  { key: 'discount',  label: 'Discount' },
  { key: 'total',     label: 'Total' },
  { key: 'paid',      label: 'Paid' },
  { key: 'balance',   label: 'Balance' },
  { key: 'status',    label: 'Status' },
  { key: 'time',      label: 'Date & Time' },
];

async function fetchSales(page = 1) {
  loading.value = true;
  try {
    const res = await salesApi.list({
      page,
      per_page:   pageSize.value,
      search:     search.value || undefined,
      status:     statusFilter.value !== 'All' ? statusFilter.value : undefined,
      order_type: billTypeFilter.value !== 'All' ? billTypeFilter.value : undefined,
    });
    orders.value  = res.data;
    summary.value = res.summary;
    meta.value    = {
      current_page: res.current_page,
      last_page:    res.last_page,
      per_page:     res.per_page,
      total:        res.total,
    };
  } finally {
    loading.value = false;
  }
}

let filterTimer = null;
function onFilterChange() {
  clearTimeout(filterTimer);
  filterTimer = setTimeout(() => fetchSales(1), 300);
}

function goPage(p) {
  if (p < 1 || p > meta.value.last_page) return;
  fetchSales(p);
}

onMounted(() => fetchSales(1));

const summaryCards = computed(() => [
  { id: 'sc-total',   label: 'Total Bills',    value: String(summary.value.total_bills),          sub: 'all transactions' },
  { id: 'sc-revenue', label: 'Total Revenue',  value: fmt(summary.value.total_revenue),            sub: 'gross sales' },
  { id: 'sc-paid',    label: 'Amount Paid',    value: fmt(summary.value.total_paid),               sub: 'collected' },
  { id: 'sc-balance', label: 'Outstanding',    value: fmt(summary.value.total_outstanding),
    sub: summary.value.total_outstanding > 0 ? 'needs collection' : 'fully settled',
    alert: summary.value.total_outstanding > 0 },
]);

const pageNumbers = computed(() => {
  const total = meta.value.last_page;
  const cur   = meta.value.current_page;
  const count = Math.min(5, total);
  let start = 1;
  if (total > 5) {
    if (cur <= 3) start = 1;
    else if (cur >= total - 2) start = total - 4;
    else start = cur - 2;
  }
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

function toggleSelect(id) {
  if (selectedIds.has(id)) selectedIds.delete(id);
  else selectedIds.add(id);
}
function toggleSelectAll() {
  if (selectedIds.size === orders.value.length) {
    orders.value.forEach(o => selectedIds.delete(o.id));
  } else {
    orders.value.forEach(o => selectedIds.add(o.id));
  }
}

function clearFilters() {
  search.value        = '';
  statusFilter.value  = 'All';
  billTypeFilter.value = 'All';
  fetchSales(1);
}

function billTypeClass(type) {
  if (type === 'Dine In')  return 'bg-info/10 text-info';
  if (type === 'Takeaway') return 'bg-accent text-primary';
  return 'bg-secondary/10 text-secondary';
}

function fmt(val) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val ?? 0);
}

function printOrder(order) {
  viewOrder.value = order;
  setTimeout(() => window.print(), 300);
}

function exportCsv() {
  const headers = ['Bill No','Sales Code','Customer','Table','Type','Tax','Discount','Total','Paid','Balance','Status','Date','Time'];
  const rows = orders.value.map(o => [
    o.billNo, o.salesCode, o.customer, o.table, o.billType,
    o.tax, o.discount, o.total, o.paid, o.balance, o.status, o.date, o.time,
  ]);
  const csv = [headers, ...rows].map(r => r.map(v => `"${v}"`).join(',')).join('\n');
  const blob = new Blob([csv], { type: 'text/csv' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = `sales-${new Date().toISOString().slice(0,10)}.csv`;
  a.click();
  URL.revokeObjectURL(url);
}
</script>

<style scoped>
.spinner {
  width: 32px; height: 32px;
  border: 3px solid var(--border); border-top-color: var(--primary);
  border-radius: 50%; animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
