<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-bold" style="color:var(--foreground)">Detail Sales Report</h1>
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
          <select v-model="reportType" class="border rounded-lg px-3 py-1.5 text-sm outline-none min-w-[160px]" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
            <option value="Detailed Sales">Detailed Sales</option>
            <option value="Consolidated Day Wise">Consolidated Day Wise</option>
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

        <div class="px-4 pb-6 mt-3 space-y-4">
          <div class="text-center">
            <p class="text-sm font-semibold" style="color:var(--foreground)">Sales Report</p>
            <p class="text-xs" style="color:var(--muted-foreground)">{{ fromDate }} - {{ toDate }}</p>
          </div>

          <!-- Bills -->
          <div v-for="bill in bills" :key="bill.salesCode" class="border rounded-lg overflow-hidden" style="border-color:var(--border)">
            <!-- Bill header -->
            <div class="grid grid-cols-4 px-3 py-2 text-xs font-semibold border-b" style="background:color-mix(in srgb,var(--muted) 50%,transparent);border-color:var(--border);color:var(--foreground)">
              <span>#{{ bill.no }}</span>
              <span>{{ bill.salesCode }}</span>
              <span>{{ bill.date }}</span>
              <span>{{ bill.customer }} | {{ bill.billType }} | Rs. {{ bill.amount }}</span>
            </div>
            <!-- Items -->
            <table class="w-full text-xs">
              <thead>
                <tr style="background:color-mix(in srgb,var(--muted) 30%,transparent)">
                  <th class="px-3 py-1.5 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">#</th>
                  <th class="px-3 py-1.5 text-left border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">DESCRIPTION</th>
                  <th class="px-3 py-1.5 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">PRICE</th>
                  <th class="px-3 py-1.5 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">QTY</th>
                  <th class="px-3 py-1.5 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">VALUE</th>
                  <th class="px-3 py-1.5 text-right border-b border-r" style="border-color:var(--border);color:var(--muted-foreground)">TAX</th>
                  <th class="px-3 py-1.5 text-right border-b" style="border-color:var(--border);color:var(--muted-foreground)">AMOUNT</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, idx) in bill.items" :key="idx" class="border-b last:border-0" style="border-color:var(--border)">
                  <td class="px-3 py-1.5 border-r" style="border-color:var(--border);color:var(--muted-foreground)">{{ idx + 1 }}</td>
                  <td class="px-3 py-1.5 border-r" style="border-color:var(--border);color:var(--foreground)">{{ item.desc }}</td>
                  <td class="px-3 py-1.5 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ item.price }}</td>
                  <td class="px-3 py-1.5 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ item.qty }}</td>
                  <td class="px-3 py-1.5 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ item.value }}</td>
                  <td class="px-3 py-1.5 text-right border-r" style="border-color:var(--border);color:var(--foreground)">{{ item.tax }}</td>
                  <td class="px-3 py-1.5 text-right" style="color:var(--foreground)">{{ item.amount }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Total -->
          <div class="flex justify-end">
            <div class="border rounded-lg px-4 py-2 text-xs font-semibold" style="border-color:var(--border);background:color-mix(in srgb,var(--primary) 8%,transparent);color:var(--foreground)">
              Total &nbsp; Rs. 2,054.00
            </div>
          </div>

          <!-- Analysis sections -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Order Analysis -->
            <AnalysisTable title="ORDER ANALYSIS" :rows="orderAnalysis" />
            <!-- Bill Type Analysis -->
            <AnalysisTable title="BILL TYPE ANALYSIS" :rows="billTypeAnalysis" />
            <!-- Pay Mode Analysis -->
            <AnalysisTable title="PAY MODE ANALYSIS" :rows="payModeAnalysis" />
            <!-- Sales By Staff -->
            <AnalysisTable title="SALES BY STAFF" :rows="salesByStaff" />
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, defineComponent, h } from 'vue';
import { RefreshCw as RefreshCwIcon, Table as TableIcon, Printer as PrinterIcon } from '@lucide/vue';

const fromDate   = ref('2021-08-10');
const toDate     = ref('2021-08-10');
const billType   = ref('');
const reportType = ref('Detailed Sales');
const generated  = ref(false);

const bills = [
  {
    no: 1, salesCode: '210810-001', date: '10-08-21', customer: 'Walkin Customer', billType: 'Cash Bill', amount: '1,129.00',
    items: [
      { desc: 'PEPPER CHICKEN GRAVY/dry', price: '200.00', qty: 1, value: '200.00', tax: '10.00', amount: '210.00' },
      { desc: 'Nattu Kozhi Biryani',      price: '230.00', qty: 1, value: '230.00', tax: '11.50', amount: '241.50' },
      { desc: 'Chicken Fried Rice',       price: '190.00', qty: 1, value: '190.00', tax: '9.50',  amount: '199.50' },
      { desc: 'Parota 1PCS',              price: '30.00',  qty: 1, value: '30.00',  tax: '1.50',  amount: '31.50'  },
      { desc: 'Lime soda sweet',          price: '60.00',  qty: 1, value: '60.00',  tax: '3.00',  amount: '63.00'  },
      { desc: 'Milk Shakes with Ice Cream', price: '175.00', qty: 1, value: '175.00', tax: '8.75', amount: '183.75' },
    ],
  },
  {
    no: 2, salesCode: '210810-002', date: '10-08-21', customer: 'Walkin Customer', billType: 'Cash Bill', amount: '389.00',
    items: [
      { desc: 'Jeera Rice',          price: '140.00', qty: 1, value: '140.00', tax: '7.00',  amount: '147.00' },
      { desc: 'PRAWN KOTHU PAROTA',  price: '230.00', qty: 1, value: '230.00', tax: '11.50', amount: '241.50' },
    ],
  },
  {
    no: 3, salesCode: '210810-003', date: '10-08-21', customer: 'Walkin Customer', billType: 'Cash Bill', amount: '536.00',
    items: [
      { desc: 'Chicken Garlic Naan',       price: '100.00', qty: 1, value: '100.00', tax: '5.00',  amount: '105.00' },
      { desc: 'Kadai Paneer/MUSHROOM',     price: '180.00', qty: 1, value: '180.00', tax: '9.00',  amount: '189.00' },
      { desc: 'Nattu Kozhi Biryani',       price: '230.00', qty: 1, value: '230.00', tax: '11.50', amount: '241.50' },
    ],
  },
];

const orderAnalysis   = [
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
const salesByStaff     = [
  { label: 'Selvakannan', amount: 'Rs. 2,054.00', count: 3 },
];

// Inline sub-component for analysis tables
const AnalysisTable = defineComponent({
  props: { title: String, rows: Array },
  setup(props) {
    return () => h('div', { class: 'border rounded-lg overflow-hidden', style: 'border-color:var(--border)' }, [
      h('div', { class: 'px-3 py-2 text-xs font-semibold text-center border-b', style: 'background:color-mix(in srgb,var(--muted) 50%,transparent);border-color:var(--border);color:var(--foreground)' }, props.title),
      h('table', { class: 'w-full text-xs' },
        h('tbody', null,
          props.rows.map(row =>
            h('tr', {
              class: 'border-b last:border-0',
              style: `border-color:var(--border);${row.total ? 'background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600' : ''}`
            }, [
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
