<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Item List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Items</p>
      </div>
      <button v-if="can('items.create')" @click="router.push({ name: 'settings-items-create' })"
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
        <Select filter v-model="perPage" @change="resetAndLoad"
          class="border rounded-lg px-2 py-1.5 text-xs outline-none"
          style="background:var(--muted);border-color:var(--border);color:var(--foreground)" :options="[{label: '10', value: 10}, {label: '25', value: 25}, {label: '50', value: 50}, {label: '100', value: 100}]" optionLabel="label" optionValue="value" />
        <span class="text-xs" style="color:var(--muted-foreground)">entries per page</span>
        <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 flex-1 min-w-[160px] max-w-xs ml-auto" style="background:var(--muted)">
          <SearchIcon :size="13" style="color:var(--muted-foreground)" />
          <input v-model="search" @input="onSearch" type="text" placeholder="Type to Search..."
            class="flex-1 bg-transparent text-xs outline-none" style="color:var(--foreground)" />
        </div>
      </div>

      <!-- AG Grid -->
      <AgGridVue
        class="items-grid"
        style="width:100%"
        :style="{ height: gridHeight }"
        :theme="gridTheme"
        :rowData="rows"
        :columnDefs="columnDefs"
        :defaultColDef="defaultColDef"
        :loading="loading"
        :suppressPaginationPanel="true"
        :suppressMovableColumns="true"
        :domLayout="'normal'"
        @grid-ready="onGridReady"
      />

      <!-- Page Pagination -->
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

<style scoped>
.items-grid { display: block; overflow: hidden; }
.pg-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  background: none;
  color: var(--muted-foreground);
}
.pg-btn:disabled { opacity: 0.35; cursor: default; }
.pg-btn.active {
  background: var(--primary);
  color: var(--primary-foreground);
}
.pg-btn:not(.active):not(:disabled):hover {
  background: color-mix(in srgb, var(--primary) 15%, transparent);
  color: var(--primary);
}
</style>

<script setup>
import { ref, computed, h, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus as PlusIcon, Search as SearchIcon } from '@lucide/vue';
import { AgGridVue } from 'ag-grid-vue3';
import { ModuleRegistry, AllCommunityModule, themeQuartz } from 'ag-grid-community';
import { usePermission } from '@/composables/usePermission.js';

const { can } = usePermission();
ModuleRegistry.registerModules([AllCommunityModule]);

const gridTheme = themeQuartz.withParams({
  backgroundColor: 'var(--card)',
  foregroundColor: 'var(--foreground)',
  borderColor: 'var(--border)',
  rowBorder: true,
  headerBackgroundColor: 'color-mix(in srgb, var(--muted) 30%, transparent)',
  headerTextColor: 'var(--muted-foreground)',
  headerFontSize: 11,
  headerFontWeight: 600,
  fontSize: 12,
  rowHeight: 40,
  headerHeight: 38,
  cellHorizontalPaddingScale: 1,
  oddRowBackgroundColor: 'transparent',
  rowHoverColor: 'color-mix(in srgb, var(--muted) 40%, transparent)',
  wrapperBorder: false,
  wrapperBorderRadius: 0,
});

import ItemDeleteModal from './ItemDeleteModal.vue';
import ItemDetailModal from './ItemDetailModal.vue';
import { itemApi } from '@/services/settingsApi';
import { toast } from 'vue-sonner';

const router = useRouter();

const rows    = ref([]);
const total   = ref(0);
const page    = ref(1);
const perPage = ref(10);
const search  = ref('');
const loading = ref(false);

const deletingItem  = ref(null);
const viewingItemId = ref(null);

let searchTimer;

// Badge renderer for state
const StateRenderer = {
  props: ['params'],
  setup(props) {
    const isOnSale = props.params.value === 'On Sale';
    return () => h('span', {
      style: isOnSale
        ? 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:500;background:color-mix(in srgb, #22c55e 12%, transparent);color:#16a34a'
        : 'display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:500;background:color-mix(in srgb, var(--danger) 12%, transparent);color:var(--danger)'
    }, props.params.value);
  }
};

// Price renderer
const PriceRenderer = {
  props: ['params'],
  setup(props) {
    return () => h('span', `Rs.${parseFloat(props.params.value).toFixed(2)}`);
  }
};

// Date renderer
const DateRenderer = {
  props: ['params'],
  setup(props) {
    return () => h('span', props.params.value ? new Date(props.params.value).toLocaleString('en-IN') : '');
  }
};

// Action cell renderer — buttons gated by permissions
const ActionRenderer = {
  props: ['params'],
  setup(props) {
    return () => {
      const buttons = [
        // View — always visible
        h('button', {
          title: 'View',
          style: 'color:var(--muted-foreground);padding:4px;border:none;background:none;cursor:pointer',
          onClick: () => viewingItemId.value = props.params.data.id,
        }, [
          h('svg', { xmlns: 'http://www.w3.org/2000/svg', width: 13, height: 13, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
            h('path', { d: 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z' }),
            h('circle', { cx: '12', cy: '12', r: '3' }),
          ]),
        ]),
      ];

      // Edit — gated
      if (can('items.edit')) {
        buttons.push(h('button', {
          title: 'Edit',
          style: 'color:var(--primary);padding:4px;border:none;background:none;cursor:pointer',
          onClick: () => router.push({ name: 'settings-items-edit', params: { id: props.params.data.id } }),
        }, [
          h('svg', { xmlns: 'http://www.w3.org/2000/svg', width: 13, height: 13, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
            h('path', { d: 'M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7' }),
            h('path', { d: 'M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z' }),
          ]),
        ]));
      }

      // Delete — gated
      if (can('items.delete')) {
        buttons.push(h('button', {
          title: 'Delete',
          style: 'color:var(--danger);padding:4px;border:none;background:none;cursor:pointer',
          onClick: () => deletingItem.value = props.params.data,
        }, [
          h('svg', { xmlns: 'http://www.w3.org/2000/svg', width: 13, height: 13, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
            h('polyline', { points: '3 6 5 6 21 6' }),
            h('path', { d: 'M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6' }),
            h('path', { d: 'M10 11v6' }),
            h('path', { d: 'M14 11v6' }),
            h('path', { d: 'M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2' }),
          ]),
        ]));
      }

      return h('div', { style: 'display:flex;gap:6px;align-items:center' }, buttons);
    };
  },
};

const columnDefs = [
  { field: 'code',              headerName: 'Item Code',       flex: 0.8, sortable: true },
  { field: 'item_name',         headerName: 'Item Name',       flex: 1.2, sortable: true },
  { field: 'category',          headerName: 'Category',        flex: 1,   sortable: true },
  { field: 'restaurant_price',  headerName: 'Rest. Price',     flex: 0.9, sortable: true, cellRenderer: PriceRenderer },
  { field: 'bar_price',         headerName: 'Bar Price',       flex: 0.9, sortable: true, cellRenderer: PriceRenderer },
  { field: 'room_price',        headerName: 'Room Price',      flex: 0.9, sortable: true, cellRenderer: PriceRenderer },
  { field: 'state',             headerName: 'State',           flex: 0.7, sortable: true, cellRenderer: StateRenderer },
  { field: 'item_type',         headerName: 'Type',            flex: 0.9, sortable: true },
  { field: 'last_accessed_by',  headerName: 'Last Accessed By', flex: 1,   sortable: true },
  { field: 'updated_at',        headerName: 'Updated At',      flex: 1.2, sortable: true, cellRenderer: DateRenderer },
  { headerName: 'Actions',      width: 130, sortable: false, cellRenderer: ActionRenderer, suppressSizeToFit: true },
];

const defaultColDef = { resizable: true, suppressMovable: true };

// Dynamic height
const gridHeight = computed(() => {
  const h = 38 + Math.max(1, rows.value.length) * 40 + 2;
  return Math.max(h, 280) + 'px';
});

const pages = computed(() => Math.ceil(total.value / perPage.value) || 1);

const pageNumbers = computed(() => {
  const totalPages = pages.value;
  const current = page.value;
  const maxVisible = 6;
  
  if (totalPages <= maxVisible) {
    return Array.from({ length: totalPages }, (_, i) => i + 1);
  }
  
  const start = Math.max(1, current - Math.floor(maxVisible / 2));
  const end = Math.min(totalPages, start + maxVisible - 1);
  
  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});

function onGridReady(params) {
  params.api.sizeColumnsToFit();
}

async function load() {
  loading.value = true;
  try {
    const res = await itemApi.list({ page: page.value, per_page: perPage.value, search: search.value });
    rows.value  = res.data;
    total.value = res.total;
  } catch (e) {
    if (e?.name !== 'CanceledError') toast.error('Failed to load items');
  } finally {
    loading.value = false;
  }
}

function resetAndLoad() {
  page.value = 1;
  load();
}

function goToPage(p) {
  if (p < 1 || p > pages.value) return;
  page.value = p;
  load();
}

function onSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(resetAndLoad, 250);
}

onMounted(load);

async function confirmDelete() {
  try {
    await itemApi.remove(deletingItem.value.id);
    toast.success('Item deleted successfully!');
    deletingItem.value = null;
    resetAndLoad();
  } catch {
    toast.error('Failed to delete item');
  }
}
</script>
