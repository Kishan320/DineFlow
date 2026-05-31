<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-bold" style="color:var(--foreground)">Cashier Report</h1>
      <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Reports · Cashier Report</p>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Cashier Report</h2>
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
        <button @click="generated = true" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium" style="background:var(--primary);color:var(--primary-foreground)">
          <RefreshCwIcon :size="13" /> Generate Report
        </button>
      </div>

      <template v-if="generated">
        <!-- Excel / Print -->
        <div class="flex justify-end gap-2 px-4 pt-3">
          <button class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg text-xs font-medium" style="border-color:var(--border);color:var(--foreground);background:var(--muted)">
            <TableIcon :size="12" /> Excel
          </button>
          <button class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg text-xs font-medium" style="border-color:var(--border);color:var(--foreground);background:var(--muted)">
            <PrinterIcon :size="12" /> Print
          </button>
        </div>

        <!-- Report Table -->
        <div class="px-4 pb-6 mt-3">
          <div class="text-center mb-3">
            <p class="text-sm font-semibold" style="color:var(--foreground)">Cashier Report</p>
            <p class="text-xs" style="color:var(--muted-foreground)">{{ fromDate }} - {{ toDate }}</p>
          </div>
          <div class="border rounded-lg overflow-hidden" style="border-color:var(--border)">
            <table class="w-full text-xs">
              <thead>
                <tr style="background:color-mix(in srgb,var(--muted) 60%,transparent)">
                  <th class="px-4 py-2 text-left font-semibold border-b border-r" style="border-color:var(--border);color:var(--foreground)">#</th>
                  <th class="px-4 py-2 text-right font-semibold border-b border-r" style="border-color:var(--border);color:var(--foreground)">AMOUNT IN RS.</th>
                  <th class="px-4 py-2 text-right font-semibold border-b" style="border-color:var(--border);color:var(--foreground)">NO. OF BILLS</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in reportRows" :key="row.label"
                  :style="row.total ? 'background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600' : ''"
                  class="border-b last:border-0" style="border-color:var(--border)">
                  <td class="px-4 py-2 border-r" :style="`border-color:var(--border);color:${row.total ? 'var(--foreground)' : 'var(--muted-foreground)'}`">{{ row.label }}</td>
                  <td class="px-4 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.amount }}</td>
                  <td class="px-4 py-2 text-right" style="color:var(--foreground)">{{ row.bills }}</td>
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

const fromDate  = ref('2021-08-10');
const toDate    = ref('2021-08-10');
const generated = ref(false);

const reportRows = [
  { label: 'Cash Bill',          amount: '2,054.00', bills: '3' },
  { label: 'Credit Bill',        amount: '0.00',     bills: '0' },
  { label: 'Guest Bill',         amount: '0.00',     bills: '0' },
  { label: 'Total Sales',        amount: '2,054.00', bills: '3', total: true },
  { label: 'Cash',               amount: '1,129.00', bills: '1' },
  { label: 'Card',               amount: '389.00',   bills: '1' },
  { label: 'Other',              amount: '0.00',     bills: '0' },
  { label: 'Total Collection',   amount: '1,516.00', bills: '2', total: true },
  { label: 'Dine In',            amount: '925.00',   bills: '2' },
  { label: 'Take away',          amount: '1,129.00', bills: '1' },
  { label: 'Delivery',           amount: '0.00',     bills: '0' },
  { label: 'Room Service',       amount: '0.00',     bills: '0' },
  { label: 'Bar Service',        amount: '0.00',     bills: '0' },
  { label: 'Total',              amount: '2,054.00', bills: '3', total: true },
  { label: 'Tax Details',        amount: '',         bills: '',  total: true },
  { label: 'GST',                amount: '97.75',    bills: '' },
  { label: 'Total Tax Amount',   amount: '97.75',    bills: '',  total: true },
];
</script>
