<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-bold" style="color:var(--foreground)">Consolidated Item Wise Report</h1>
      <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Reports · Detailed Sales</p>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Detail Sales Report</h2>
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
          <select v-model="billType" class="border rounded-lg px-3 py-1.5 text-sm outline-none min-w-[140px]" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
            <option value="">– All Type –</option>
            <option value="Cash Bill">Cash Bill</option>
            <option value="Credit Bill">Credit Bill</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium mb-1" style="color:var(--foreground)">Report Type</label>
          <select v-model="reportType" class="border rounded-lg px-3 py-1.5 text-sm outline-none min-w-[200px]" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
            <option value="Consolidated Day Wise">Consolidated Day Wise</option>
            <option value="Detailed Sales">Detailed Sales</option>
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
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">DATE</th>
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">DESCRIPTION</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">PRICE</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">QTY</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">VALUE</th>
                  <th class="px-3 py-2 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">TAX</th>
                  <th class="px-3 py-2 text-right border-b" style="border-color:var(--border);color:var(--muted-foreground)">AMOUNT</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in rows" :key="i" class="border-b" style="border-color:var(--border)">
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--muted-foreground)">{{ i + 1 }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.date }}</td>
                  <td class="px-3 py-2 border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.desc }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.price }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.qty }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.value }}</td>
                  <td class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ row.tax }}</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">{{ row.amount }}</td>
                </tr>
                <!-- Total -->
                <tr style="background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600">
                  <td colspan="7" class="px-3 py-2 text-right border-r" style="border-color:var(--border);color:var(--foreground)">Total</td>
                  <td class="px-3 py-2 text-right" style="color:var(--foreground)">Rs. 2052.75</td>
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

const fromDate   = ref('2021-08-10');
const toDate     = ref('2021-08-10');
const billType   = ref('');
const reportType = ref('Consolidated Day Wise');
const generated  = ref(false);

const rows = [
  { date: '10-08-21', desc: 'PEPPER CHICKEN GRAVY/dry',  price: '200.00', qty: 1, value: '200.00', tax: '10.00', amount: '210.00'  },
  { date: '10-08-21', desc: 'Milk Shakes with Ice Cream', price: '175.00', qty: 1, value: '175.00', tax: '8.75',  amount: '183.75'  },
  { date: '10-08-21', desc: 'Lime soda sweet',            price: '60.00',  qty: 1, value: '60.00',  tax: '3.00',  amount: '63.00'   },
  { date: '10-08-21', desc: 'Parota 1PCS',                price: '30.00',  qty: 1, value: '30.00',  tax: '1.50',  amount: '31.50'   },
  { date: '10-08-21', desc: 'Egg Biyani',                 price: '190.00', qty: 1, value: '190.00', tax: '9.50',  amount: '199.50'  },
  { date: '10-08-21', desc: 'chicken fried rice',         price: '190.00', qty: 1, value: '190.00', tax: '9.50',  amount: '199.50'  },
  { date: '10-08-21', desc: 'Nattu Kozhi Briyani',        price: '230.00', qty: 2, value: '460.00', tax: '23.00', amount: '483.00'  },
  { date: '10-08-21', desc: 'PRAWN KOTHU PAROTA',         price: '230.00', qty: 1, value: '230.00', tax: '11.50', amount: '241.50'  },
  { date: '10-08-21', desc: 'Jeera Rice',                 price: '140.00', qty: 1, value: '140.00', tax: '7.00',  amount: '147.00'  },
  { date: '10-08-21', desc: 'Kadai Paneer/MUSHROOM',      price: '180.00', qty: 1, value: '180.00', tax: '9.00',  amount: '189.00'  },
  { date: '10-08-21', desc: 'Cheese Garlic Naan',         price: '100.00', qty: 1, value: '100.00', tax: '5.00',  amount: '105.00'  },
];
</script>
