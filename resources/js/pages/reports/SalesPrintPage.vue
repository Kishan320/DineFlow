<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-bold" style="color:var(--foreground)">Sales Print Report</h1>
      <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Reports · Sales Print Report</p>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Daily Item Sales Report</h2>
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
            <option value="">– Select Bill Type –</option>
            <option value="Cash Bill">Cash Bill</option>
            <option value="Credit Bill">Credit Bill</option>
          </select>
        </div>
        <button @click="generated = true" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium" style="background:var(--primary);color:var(--primary-foreground)">
          <RefreshCwIcon :size="13" /> Generate Report
        </button>
      </div>

      <template v-if="generated">
        <div class="flex justify-end gap-2 px-4 pt-3">
          <button class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg text-xs font-medium" style="border-color:var(--border);color:var(--foreground);background:var(--muted)">
            <PrinterIcon :size="12" /> Print
          </button>
        </div>

        <div class="px-4 pb-6 mt-3 space-y-4">
          <div class="text-center">
            <p class="text-sm font-semibold" style="color:var(--foreground)">Item Sales Report</p>
            <p class="text-xs" style="color:var(--muted-foreground)">{{ fromDate }} - {{ toDate }}</p>
          </div>

          <!-- Bills grouped -->
          <div class="border rounded-lg overflow-hidden" style="border-color:var(--border)">
            <table class="w-full text-xs">
              <thead>
                <tr style="background:color-mix(in srgb,var(--muted) 60%,transparent)">
                  <th class="px-3 py-2 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">ENTRIES</th>
                  <th class="px-3 py-2 text-right border-b" style="border-color:var(--border);color:var(--muted-foreground)">AMOUNT</th>
                </tr>
              </thead>
              <tbody>
                <template v-for="bill in bills" :key="bill.salesCode">
                  <!-- Bill header row -->
                  <tr style="background:color-mix(in srgb,var(--muted) 40%,transparent)">
                    <td class="px-3 py-1.5 font-semibold border-b border-r" style="border-color:var(--border);color:var(--foreground)">
                      {{ bill.date }} · {{ bill.salesCode }}
                    </td>
                    <td class="px-3 py-1.5 text-right font-semibold border-b" style="border-color:var(--border);color:var(--foreground)">Rs. {{ bill.amount }}</td>
                  </tr>
                  <!-- Item rows -->
                  <tr v-for="(item, idx) in bill.items" :key="idx" class="border-b last:border-0" style="border-color:var(--border)">
                    <td class="px-3 py-1 border-r pl-6" style="border-color:var(--border);color:var(--muted-foreground)">
                      {{ item.qty }} x {{ item.desc }}
                    </td>
                    <td class="px-3 py-1 text-right" style="color:var(--foreground)">Rs. {{ item.amount }}</td>
                  </tr>
                </template>
                <!-- Total -->
                <tr style="background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600">
                  <td class="px-3 py-2 text-right border-r border-t" style="border-color:var(--border);color:var(--foreground)">Total</td>
                  <td class="px-3 py-2 text-right border-t" style="border-color:var(--border);color:var(--foreground)">Rs. 2,054.00</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Analysis sections -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <AnalysisTable title="ORDER ANALYSIS"    :rows="orderAnalysis" />
            <AnalysisTable title="BILL TYPE ANALYSIS" :rows="billTypeAnalysis" />
            <AnalysisTable title="PAY MODE ANALYSIS"  :rows="payModeAnalysis" />
            <AnalysisTable title="SALES BY STAFF"     :rows="salesByStaff" />
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, defineComponent, h } from 'vue';
import { RefreshCw as RefreshCwIcon, Printer as PrinterIcon } from '@lucide/vue';

const fromDate  = ref('2021-08-10');
const toDate    = ref('2021-08-10');
const billType  = ref('');
const generated = ref(false);

const bills = [
  {
    salesCode: '210810-001', date: '10-Aug-2021', amount: '1,129.00',
    items: [
      { qty: 1, desc: 'PEPPER CHICKEN GRAVY/dry',    amount: '210.00' },
      { qty: 1, desc: 'Nattu Kozhi Biryani',          amount: '241.50' },
      { qty: 1, desc: 'Chicken Fried Rice',            amount: '199.50' },
      { qty: 1, desc: 'Parota 1PCS',                   amount: '31.50'  },
      { qty: 1, desc: 'Lime soda sweet',               amount: '63.00'  },
      { qty: 1, desc: 'Milk Shakes with Ice Cream',    amount: '183.75' },
    ],
  },
  {
    salesCode: '210810-002', date: '10-Aug-2021', amount: '389.00',
    items: [
      { qty: 1, desc: 'Jeera Rice',         amount: '147.00' },
      { qty: 1, desc: 'PRAWN KOTHU PAROTA', amount: '241.50' },
    ],
  },
  {
    salesCode: '210810-003', date: '10-Aug-2021', amount: '536.00',
    items: [
      { qty: 1, desc: 'Chicken Garlic Naan',   amount: '105.00' },
      { qty: 1, desc: 'Kadai Paneer/MUSHROOM', amount: '189.00' },
      { qty: 1, desc: 'Nattu Kozhi Biryani',   amount: '241.50' },
    ],
  },
];

const orderAnalysis    = [
  { label: 'Completed Orders',      amount: 'Rs. 1,518.00', count: 2 },
  { label: 'Partially Paid Orders', amount: 'Rs. 536.00',   count: 1 },
  { label: 'Total',                 amount: 'Rs. 2,054.00', count: 3, total: true },
];
const billTypeAnalysis = [
  { label: 'Cash Bill',   amount: 'Rs. 2,054.00', count: 3 },
  { label: 'Credit Bill', amount: 'Rs. 0.00',     count: 0 },
  { label: 'Guest Bill',  amount: 'Rs. 0.00',     count: 0 },
  { label: 'Total',       amount: 'Rs. 2,054.00', count: 3, total: true },
];
const payModeAnalysis  = [
  { label: 'Cash',  amount: 'Rs. 1,129.00', count: 1 },
  { label: 'Card',  amount: 'Rs. 389.00',   count: 1 },
  { label: 'Other', amount: 'Rs. 0.00',     count: 0 },
  { label: 'Total', amount: 'Rs. 1,518.00', count: 2, total: true },
];
const salesByStaff     = [{ label: 'Selvakannan', amount: 'Rs. 2,054.00', count: 3 }];

const AnalysisTable = defineComponent({
  props: { title: String, rows: Array },
  setup(props) {
    return () => h('div', { class: 'border rounded-lg overflow-hidden', style: 'border-color:var(--border)' }, [
      h('div', { class: 'px-3 py-2 text-xs font-semibold text-center border-b', style: 'background:color-mix(in srgb,var(--muted) 50%,transparent);border-color:var(--border);color:var(--foreground)' }, props.title),
      h('table', { class: 'w-full text-xs' },
        h('tbody', null,
          props.rows.map(row =>
            h('tr', { class: 'border-b last:border-0', style: `border-color:var(--border);${row.total ? 'background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600' : ''}` }, [
              h('td', { class: 'px-3 py-1.5 border-r', style: 'border-color:var(--border);color:var(--foreground)' }, row.label),
              h('td', { class: 'px-3 py-1.5 text-right border-r', style: 'border-color:var(--border);color:var(--foreground)' }, row.amount),
              h('td', { class: 'px-3 py-1.5 text-right', style: 'color:var(--muted-foreground)' }, row.count ?? ''),
            ])
          )
        )
      ),
    ]);
  },
});
</script>
