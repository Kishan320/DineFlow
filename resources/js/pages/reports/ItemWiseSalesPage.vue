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
            <option v-for="item in itemOptions" :key="item" :value="item">{{ item }}</option>
          </select>
        </div>
        <button @click="generated = true" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium" style="background:var(--primary);color:var(--primary-foreground)">
          <RefreshCwIcon :size="13" /> Generate Report
        </button>
      </div>

      <template v-if="generated">
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
            <p class="text-xs" style="color:var(--muted-foreground)">{{ fromDate }} - {{ toDate }} | {{ selectedItem }}</p>
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
                <tr v-for="(row, i) in filteredRows" :key="row.salesCode" class="border-b" style="border-color:var(--border)">
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--muted-foreground)">{{ i + 1 }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.billNo }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.salesCode }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.date }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.qty }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ row.salesValue }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ row.taxAmount }}</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">Rs. {{ row.salesAmount }}</td>
                </tr>
                <!-- Total -->
                <tr style="background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600">
                  <td colspan="5" class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Total</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. 460.00</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. 23.00</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">Rs. 483.00</td>
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
import { ref, computed } from 'vue';
import { RefreshCw as RefreshCwIcon, Table as TableIcon, Printer as PrinterIcon } from '@lucide/vue';

const fromDate    = ref('2021-08-10');
const toDate      = ref('2021-08-10');
const generated   = ref(false);

const itemOptions = [
  'Nattu Kozhi Biryani', 'Chicken Biryani', 'Mutton Biryani', 'Veg Biryani',
  'Masala Dosa', 'Chicken 65', 'Paneer Butter Masala', 'Butter Naan',
];
const selectedItem = ref('Nattu Kozhi Biryani');

const allRows = [
  { billNo: 1, salesCode: '210810-001', date: '10-08-21', item: 'Nattu Kozhi Biryani', qty: 1, salesValue: '230.00', taxAmount: '11.50', salesAmount: '241.50' },
  { billNo: 3, salesCode: '210810-003', date: '10-08-21', item: 'Nattu Kozhi Biryani', qty: 1, salesValue: '230.00', taxAmount: '11.50', salesAmount: '241.50' },
  { billNo: 2, salesCode: '210810-002', date: '10-08-21', item: 'Chicken Biryani',     qty: 2, salesValue: '360.00', taxAmount: '18.00', salesAmount: '378.00' },
];

const filteredRows = computed(() => allRows.filter(r => r.item === selectedItem.value));
</script>
