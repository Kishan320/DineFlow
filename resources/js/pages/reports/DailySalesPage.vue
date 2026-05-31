<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-bold" style="color:var(--foreground)">Daily Sales Report</h1>
      <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Reports · Daily Sales</p>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Daily Sales Report</h2>
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
          <label class="block text-xs font-medium mb-1" style="color:var(--foreground)">Bill Type</label>
          <select v-model="billType" class="border rounded-lg px-3 py-1.5 text-sm outline-none min-w-[160px]" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
            <option value="">Select an Option</option>
            <option value="Cash Bill">Cash Bill</option>
            <option value="Credit Bill">Credit Bill</option>
            <option value="Guest Bill">Guest Bill</option>
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
            <p class="text-xs" style="color:var(--muted-foreground)">{{ fromDate }} - {{ toDate }}</p>
          </div>
          <div class="border rounded-lg overflow-x-auto" style="border-color:var(--border)">
            <table class="w-full text-xs">
              <thead>
                <tr style="background:color-mix(in srgb,var(--muted) 60%,transparent)">
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">#</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">BILL NO</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">SALES CODE</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">DATE</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">CUSTOMER</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">BILL TYPE</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">BILL AMOUNT</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">TAX AMOUNT</th>
                  <th class="px-3 py-2 text-center border-b" colspan="3" style="border-color:var(--border);color:var(--muted-foreground)">PAYMENT</th>
                </tr>
                <tr style="background:color-mix(in srgb,var(--muted) 60%,transparent)">
                  <th colspan="8" class="border-b border-r" style="border-color:var(--border)"></th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">CASH</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">CARD</th>
                  <th class="px-3 py-2 text-right border-b" style="border-color:var(--border);color:var(--muted-foreground)">OTHERS</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in rows" :key="row.salesCode" class="border-b" style="border-color:var(--border)">
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--muted-foreground)">{{ i + 1 }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.billNo }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.salesCode }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.date }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.customer }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.billType }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ row.billAmount }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. {{ row.taxAmount }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.cash ? 'Rs. ' + row.cash : '' }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.card ? 'Rs. ' + row.card : '' }}</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">{{ row.others ? 'Rs. ' + row.others : '' }}</td>
                </tr>
                <!-- Total row -->
                <tr style="background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600">
                  <td colspan="6" class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Total</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. 2054.00</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. 97.75</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. 1129.00</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Rs. 389.00</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">Rs. 0.00</td>
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
const billType  = ref('');
const generated = ref(false);

const rows = [
  { billNo: 1, salesCode: '210810-001', date: '10-08-21', customer: 'Walkin Customer', billType: 'Cash Bill', billAmount: '1,129.00', taxAmount: '53.75', cash: '1,129.00', card: '',      others: '' },
  { billNo: 2, salesCode: '210810-002', date: '10-08-21', customer: 'Walkin Customer', billType: 'Cash Bill', billAmount: '389.00',   taxAmount: '18.50', cash: '',         card: '389.00', others: '' },
  { billNo: 3, salesCode: '210810-003', date: '10-08-21', customer: 'Walkin Customer', billType: 'Cash Bill', billAmount: '536.00',   taxAmount: '25.50', cash: '',         card: '',       others: '' },
];
</script>
