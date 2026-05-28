<template>
  <div>
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div
        v-for="card in summaryCards"
        :key="card.id"
        :class="['border rounded-xl p-4 shadow-card', card.alert ? 'border-danger/30 bg-danger/5' : 'border-border']"
        style="background:var(--card)"
      >
        <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--muted-foreground)">{{ card.label }}</p>
        <p :class="['text-xl font-bold tabular-nums', card.alert ? 'text-danger' : '']" :style="card.alert ? 'color:var(--danger)' : 'color:var(--foreground)'">{{ card.value }}</p>
        <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">{{ card.sub }}</p>
      </div>
    </div>

    <!-- Filters + Table -->
    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <!-- Filters bar -->
      <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b" style="border-color:var(--border)">
        <!-- Search -->
        <div class="flex items-center gap-2 rounded-lg px-3 py-2 flex-1 min-w-48" style="background:var(--muted)">
          <SearchIcon :size="14" class="flex-shrink-0" style="color:var(--muted-foreground)" />
          <input v-model="search" @input="page = 1" type="text" placeholder="Search bill no, customer, sales code..." class="flex-1 bg-transparent text-sm outline-none" style="color:var(--foreground)" />
          <button v-if="search" @click="search = ''" style="color:var(--muted-foreground)"><XIcon :size="13" /></button>
        </div>

        <!-- Status filter -->
        <div class="flex items-center gap-1.5 flex-wrap">
          <button v-for="s in statuses" :key="s" @click="statusFilter = s; page = 1" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors" :style="statusFilter === s ? 'background:var(--primary);color:var(--primary-foreground)' : 'background:var(--muted);color:var(--muted-foreground)'">{{ s }}</button>
        </div>

        <!-- Bill type filter -->
        <div class="flex items-center gap-1.5 flex-wrap">
          <button v-for="t in billTypes" :key="t" @click="billTypeFilter = t; page = 1" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors" :style="billTypeFilter === t ? 'background:var(--secondary);color:var(--secondary-foreground)' : 'background:var(--muted);color:var(--muted-foreground)'">{{ t }}</button>
        </div>

        <button class="ml-auto flex items-center gap-1.5 px-3 py-2 rounded-lg border text-xs font-medium hover:bg-muted transition-colors flex-shrink-0" style="border-color:var(--border);color:var(--foreground)">
          <DownloadIcon :size="14" /> Export
        </button>
      </div>

      <!-- Bulk action bar -->
      <div v-if="selectedIds.size > 0" class="flex items-center gap-3 px-4 py-2.5 border-b slide-up" style="background:color-mix(in srgb, var(--primary) 5%, transparent);border-color:color-mix(in srgb, var(--primary) 20%, transparent)">
        <span class="text-xs font-semibold" style="color:var(--primary)">{{ selectedIds.size }} selected</span>
        <button class="flex items-center gap-1.5 text-xs font-medium hover:text-primary transition-colors" style="color:var(--foreground)"><PrinterIcon :size="13" /> Print Selected</button>
        <button class="flex items-center gap-1.5 text-xs font-medium hover:text-primary transition-colors" style="color:var(--foreground)"><DownloadIcon :size="13" /> Download</button>
        <button @click="selectedIds.clear()" class="ml-auto text-xs hover:text-foreground transition-colors" style="color:var(--muted-foreground)">Clear selection</button>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b" style="border-color:var(--border);background:color-mix(in srgb, var(--muted) 30%, transparent)">
              <th class="px-4 py-3 w-10">
                <input type="checkbox" :checked="selectedIds.size === paginated.length && paginated.length > 0" @change="toggleSelectAll" class="rounded accent-primary" style="border-color:var(--border)" />
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
            <tr v-if="paginated.length === 0">
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
              v-for="(order, idx) in paginated"
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
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm tabular-nums" style="color:var(--foreground)">${{ order.tax.toFixed(2) }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span :class="['text-sm tabular-nums', order.discount > 0 ? 'font-medium' : '']" :style="order.discount > 0 ? 'color:var(--success)' : 'color:var(--muted-foreground)'">{{ order.discount > 0 ? `-$${order.discount.toFixed(2)}` : '—' }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm font-semibold tabular-nums" style="color:var(--foreground)">${{ order.total.toFixed(2) }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span class="text-sm font-medium tabular-nums" style="color:var(--success)">${{ order.paid.toFixed(2) }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><span :class="['text-sm tabular-nums font-medium']" :style="order.balance > 0 ? 'color:var(--danger)' : 'color:var(--muted-foreground)'">{{ order.balance > 0 ? `$${order.balance.toFixed(2)}` : '—' }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap"><StatusBadge :status="order.status" /></td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-xs font-medium" style="color:var(--foreground)">{{ order.date }}</span>
                  <span class="text-xs" style="color:var(--muted-foreground)">{{ order.time }}</span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="viewOrder = order" class="p-1.5 rounded-lg hover:bg-info/10 transition-colors" style="color:var(--muted-foreground)" title="View"><EyeIcon :size="14" /></button>
                  <button class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)" title="Print"><PrinterIcon :size="14" /></button>
                  <button class="p-1.5 rounded-lg hover:bg-warning/10 transition-colors" style="color:var(--muted-foreground)" title="Edit"><EditIcon :size="14" /></button>
                  <button class="p-1.5 rounded-lg hover:bg-secondary/10 transition-colors" style="color:var(--muted-foreground)" title="Download"><FileDownIcon :size="14" /></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t" style="border-color:var(--border)">
        <div class="flex items-center gap-3 text-xs" style="color:var(--muted-foreground)">
          <span>Showing {{ filtered.length === 0 ? 0 : (page - 1) * pageSize + 1 }}–{{ Math.min(page * pageSize, filtered.length) }} of {{ filtered.length }} records</span>
          <div class="flex items-center gap-1.5">
            <span>Rows per page:</span>
            <select v-model.number="pageSize" @change="page = 1" class="border rounded-lg px-2 py-1 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
              <option v-for="n in [10, 25, 50]" :key="n" :value="n">{{ n }}</option>
            </select>
          </div>
        </div>
        <div class="flex items-center gap-1">
          <button @click="page = 1" :disabled="page === 1" class="px-2 py-1.5 rounded-lg text-xs font-medium hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">First</button>
          <button @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)"><ChevronLeftIcon :size="14" /></button>
          <button
            v-for="pn in pageNumbers"
            :key="pn"
            @click="page = pn"
            class="w-8 h-8 rounded-lg text-xs font-medium transition-colors"
            :style="page === pn ? 'background:var(--primary);color:var(--primary-foreground)' : 'color:var(--muted-foreground)'"
          >{{ pn }}</button>
          <button @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages || totalPages === 0" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)"><ChevronRightIcon :size="14" /></button>
          <button @click="page = totalPages" :disabled="page === totalPages || totalPages === 0" class="px-2 py-1.5 rounded-lg text-xs font-medium hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">Last</button>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <SalesDetailModal v-if="viewOrder" :order="viewOrder" @close="viewOrder = null" />
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { Search as SearchIcon, X as XIcon, Download as DownloadIcon, Filter as FilterIcon, Eye as EyeIcon, Printer as PrinterIcon, Edit as EditIcon, FileDown as FileDownIcon, ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon, ArrowUpDown, ArrowUp, ArrowDown } from '@lucide/vue';
import StatusBadge from '@/components/ui/StatusBadge.vue';
import SalesDetailModal from './SalesDetailModal.vue';
import { recentOrders } from '@/utils/mockData.js';

const search = ref('');
const statusFilter = ref('All');
const billTypeFilter = ref('All');
const sortKey = ref('date');
const sortDir = ref('desc');
const page = ref(1);
const pageSize = ref(10);
const selectedIds = reactive(new Set());
const viewOrder = ref(null);

const statuses = ['All', 'Paid', 'Pending', 'Draft', 'Cancelled'];
const billTypes = ['All', 'Dine In', 'Takeaway', 'Delivery'];

const columns = [
  { key: 'billNo', label: 'Bill No' },
  { key: 'salesCode', label: 'Sales Code' },
  { key: 'customer', label: 'Customer' },
  { key: 'table', label: 'Table / Type' },
  { key: 'billType', label: 'Bill Type' },
  { key: 'tax', label: 'Tax' },
  { key: 'discount', label: 'Discount' },
  { key: 'total', label: 'Total' },
  { key: 'paid', label: 'Paid' },
  { key: 'balance', label: 'Balance' },
  { key: 'status', label: 'Status' },
  { key: 'time', label: 'Date & Time' },
];

const allOrders = [
  ...recentOrders,
  ...recentOrders.map((o, i) => ({
    ...o,
    id: `${o.id}-dup-${i}`,
    billNo: `BILL-${2800 + i}`,
    salesCode: `SC-${2800 + i}`,
    date: '05/26/2026',
    time: `${10 + i}:${String(i * 7 % 60).padStart(2, '0')}`,
  })),
];

const filtered = computed(() => {
  let data = allOrders.filter(o => {
    const matchSearch = !search.value ||
      o.billNo.toLowerCase().includes(search.value.toLowerCase()) ||
      o.customer.toLowerCase().includes(search.value.toLowerCase()) ||
      o.salesCode.toLowerCase().includes(search.value.toLowerCase());
    const matchStatus = statusFilter.value === 'All' || o.status === statusFilter.value;
    const matchType = billTypeFilter.value === 'All' || o.billType === billTypeFilter.value;
    return matchSearch && matchStatus && matchType;
  });

  if (sortKey.value) {
    data = [...data].sort((a, b) => {
      const av = a[sortKey.value], bv = b[sortKey.value];
      if (typeof av === 'number' && typeof bv === 'number') return sortDir.value === 'asc' ? av - bv : bv - av;
      return sortDir.value === 'asc' ? String(av ?? '').localeCompare(String(bv ?? '')) : String(bv ?? '').localeCompare(String(av ?? ''));
    });
  }
  return data;
});

const totalPages = computed(() => Math.ceil(filtered.value.length / pageSize.value));
const paginated = computed(() => filtered.value.slice((page.value - 1) * pageSize.value, page.value * pageSize.value));

const pageNumbers = computed(() => {
  const total = totalPages.value;
  const cur = page.value;
  const count = Math.min(5, total);
  let start = 1;
  if (total > 5) {
    if (cur <= 3) start = 1;
    else if (cur >= total - 2) start = total - 4;
    else start = cur - 2;
  }
  return Array.from({ length: count }, (_, i) => start + i);
});

const summaryCards = computed(() => {
  const totalRevenue = filtered.value.reduce((s, o) => s + o.total, 0);
  const totalPaid = filtered.value.reduce((s, o) => s + o.paid, 0);
  const totalBalance = filtered.value.reduce((s, o) => s + o.balance, 0);
  return [
    { id: 'sc-total', label: 'Total Bills', value: String(filtered.value.length), sub: 'all transactions' },
    { id: 'sc-revenue', label: 'Total Revenue', value: `$${totalRevenue.toFixed(2)}`, sub: 'gross sales' },
    { id: 'sc-paid', label: 'Amount Paid', value: `$${totalPaid.toFixed(2)}`, sub: 'collected' },
    { id: 'sc-balance', label: 'Outstanding', value: `$${totalBalance.toFixed(2)}`, sub: totalBalance > 0 ? 'needs collection' : 'fully settled', alert: totalBalance > 0 },
  ];
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
  if (selectedIds.size === paginated.value.length) {
    paginated.value.forEach(o => selectedIds.delete(o.id));
  } else {
    paginated.value.forEach(o => selectedIds.add(o.id));
  }
}

function clearFilters() {
  search.value = '';
  statusFilter.value = 'All';
  billTypeFilter.value = 'All';
}

function billTypeClass(type) {
  if (type === 'Dine In') return 'bg-info/10 text-info';
  if (type === 'Takeaway') return 'bg-accent text-primary';
  return 'bg-secondary/10 text-secondary';
}
</script>
