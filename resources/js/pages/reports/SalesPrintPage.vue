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
        <button @click="generate" :disabled="loading" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium" style="background:var(--primary);color:var(--primary-foreground)">
          <RefreshCwIcon :size="13" :class="loading ? 'animate-spin' : ''" /> Generate Report
        </button>
      </div>

      <div v-if="error" class="px-4 py-3 text-sm text-red-500">{{ error }}</div>

      <template v-if="generated && !loading">
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
                  <tr style="background:color-mix(in srgb,var(--muted) 40%,transparent)">
                    <td class="px-3 py-1.5 font-semibold border-b border-r" style="border-color:var(--border);color:var(--foreground)">
                      {{ bill.date }} · {{ bill.salesCode }}
                    </td>
                    <td class="px-3 py-1.5 text-right font-semibold border-b" style="border-color:var(--border);color:var(--foreground)">Rs. {{ bill.amount }}</td>
                  </tr>
                  <tr v-for="(item, idx) in bill.items" :key="idx" class="border-b last:border-0" style="border-color:var(--border)">
                    <td class="px-3 py-1 border-r pl-6" style="border-color:var(--border);color:var(--muted-foreground)">
                      {{ item.qty }} x {{ item.desc }}
                    </td>
                    <td class="px-3 py-1 text-right" style="color:var(--foreground)">Rs. {{ item.amount }}</td>
                  </tr>
                </template>
                <tr style="background:color-mix(in srgb,var(--primary) 8%,transparent);font-weight:600">
                  <td class="px-3 py-2 text-right border-r border-t" style="border-color:var(--border);color:var(--foreground)">Total</td>
                  <td class="px-3 py-2 text-right border-t" style="border-color:var(--border);color:var(--foreground)">Rs. {{ grandTotal }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="analysis" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <AnalysisTable title="ORDER ANALYSIS"     :rows="analysis.order" />
            <AnalysisTable title="BILL TYPE ANALYSIS" :rows="analysis.bill_type" />
            <AnalysisTable title="PAY MODE ANALYSIS"  :rows="analysis.pay_mode" />
            <AnalysisTable title="SALES BY STAFF"     :rows="analysis.staff" />
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, defineComponent, h } from 'vue';
import { RefreshCw as RefreshCwIcon, Printer as PrinterIcon } from '@lucide/vue';
import { reportApi } from '@/services/settingsApi';

const today     = new Date().toISOString().slice(0, 10);
const fromDate  = ref(today);
const toDate    = ref(today);
const billType  = ref('');
const generated = ref(false);
const loading   = ref(false);
const error     = ref('');
const bills     = ref([]);
const analysis  = ref(null);
const grandTotal = ref('0.00');

async function generate() {
  loading.value = true;
  error.value   = '';
  try {
    const { data } = await reportApi.salesPrint({ from_date: fromDate.value, to_date: toDate.value, bill_type: billType.value || undefined });
    bills.value      = data.bills;
    analysis.value   = data.analysis;
    grandTotal.value = data.grandTotal;
    generated.value  = true;
  } catch (e) {
    error.value = e?.response?.data?.message ?? 'Failed to load report.';
  } finally {
    loading.value = false;
  }
}

const AnalysisTable = defineComponent({
  props: { title: String, rows: Array },
  setup(props) {
    return () => h('div', { class: 'border rounded-lg overflow-hidden', style: 'border-color:var(--border)' }, [
      h('div', { class: 'px-3 py-2 text-xs font-semibold text-center border-b', style: 'background:color-mix(in srgb,var(--muted) 50%,transparent);border-color:var(--border);color:var(--foreground)' }, props.title),
      h('table', { class: 'w-full text-xs' },
        h('tbody', null,
          (props.rows ?? []).map(row =>
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
