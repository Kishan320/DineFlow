<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-bold" style="color:var(--foreground)">Item Wise Sales Report</h1>
      <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Reports · Item Wise Sales</p>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Sales Report - Item</h2>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap items-end gap-4 px-4 py-4 border-b" style="border-color:var(--border)">
        <div>
          <label class="block text-xs font-medium mb-1" style="color:var(--foreground)">From Date</label>
          <input v-model="fromDate" type="date" class="border rounded-lg px-3 py-1.5 text-sm outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)" />
        </div>
        <div>
          <label class="block text-xs font-medium mb-1" style="color:var(--foreground)">To Date</label>
          <input v-model="toDate" type="date" class="border rounded-lg px-3 py-1.5 text-sm outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)" />
        </div>
        <div>
          <label class="block text-xs font-medium mb-1" style="color:var(--foreground)">Item Name</label>
          <select v-model="selectedItem" class="border rounded-lg px-3 py-1.5 text-sm outline-none min-w-[200px]" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
            <option value="">– All Items –</option>
            <option v-for="item in itemOptions" :key="item" :value="item">{{ item }}</option>
          </select>
        </div>
        <button @click="generate" :disabled="loading" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium" style="background:var(--primary);color:var(--primary-foreground)">
          <RefreshCwIcon :size="13" :class="loading ? 'animate-spin' : ''" /> Generate Report
        </button>
      </div>

      <div v-if="error" class="px-4 py-3 text-sm text-red-500">{{ error }}</div>

      <template v-if="generated && !loading">
        <div class="flex justify-end gap-2 px-4 pt-3">
          <button class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg text-xs font-medium" style="border-color:var(--border);color:var(--foreground);background:var(--muted)">
            <TableIcon :size="12" /> Excel
          </button>
          <button class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg text-xs font-medium" style="border-color:var(--border);color:var(--foreground);background:var(--muted)">
            <PrinterIcon :size="12" /> Print
          </button>
        </div>

        <div class="px-4 pb-6 mt-3">
          <div class="text-center mb-3">
            <p class="text-sm font-semibold" style="color:var(--foreground)">Sales Report</p>
            <p class="text-xs" style="color:var(--muted-foreground)">{{ fromDate }} - {{ toDate }}{{ selectedItem ? ' | ' + selectedItem : '' }}</p>
          </div>
          <div class="border rounded-lg overflow-x-auto" style="border-color:var(--border)">
            <table class="w-full text-xs">
              <thead>
                <tr style="background:color-mix(in srgb,var(--muted) 60%,transparent)">
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">#</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">BILL NO</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">SALES CODE</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">DATE</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">QUANTITY</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">SALES VALUE</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">TAX AMOUNT</th>
                  <th class="px-3 py-2 text-right border-b" style="border-color:var(--border);color:var(--muted-foreground)">SALES AMOUNT</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in rows" :key="i" class="border-b" style="border-color:var(--border)">
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--muted-foreground)">{{ i + 1 }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.billNo }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.salesCode }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.date }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.qty }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ row.salesValue }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ row.taxAmount }}</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">Rs. {{ row.salesAmount }}</td>
                </tr>
                <tr v-if="totals" style="background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600">
                  <td colspan="4" class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Total</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ totals.qty }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ totals.salesValue }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ totals.taxAmount }}</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">Rs. {{ totals.salesAmount }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RefreshCw as RefreshCwIcon, Table as TableIcon, Printer as PrinterIcon } from '@lucide/vue';
import { reportApi } from '@/services/settingsApi';

const today        = new Date().toISOString().slice(0, 10);
const fromDate     = ref(today);
const toDate       = ref(today);
const selectedItem = ref('');
const itemOptions  = ref([]);
const generated    = ref(false);
const loading      = ref(false);
const error        = ref('');
const rows         = ref([]);
const totals       = ref(null);

async function generate() {
  loading.value = true;
  error.value   = '';
  try {
    const { data } = await reportApi.itemWise({
      from_date:  fromDate.value,
      to_date:    toDate.value,
      item_name:  selectedItem.value || undefined,
    });
    rows.value        = data.data;
    totals.value      = data.totals;
    itemOptions.value = data.itemNames ?? itemOptions.value;
    generated.value   = true;
  } catch (e) {
    error.value = e?.response?.data?.message ?? 'Failed to load report.';
  } finally {
    loading.value = false;
  }
}
</script>
