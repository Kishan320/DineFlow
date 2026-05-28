<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 fade-in">
    <div
      class="border rounded-xl shadow-modal w-full max-w-md flex flex-col slide-up"
      style="background:var(--card);border-color:var(--border);max-height:90vh"
    >
      <!-- Header -->
      <div
        class="flex items-center justify-between px-5 py-3 border-b flex-shrink-0"
        style="border-color:var(--border)"
      >
        <h3 class="font-semibold text-sm" style="color:var(--foreground)">
          Todays Sales {{ todayLabel }}
        </h3>
        <button
          @click="$emit('close')"
          class="p-1.5 rounded-lg hover:bg-muted transition-colors"
          style="color:var(--muted-foreground)"
        >
          <XIcon :size="16" />
        </button>
      </div>

      <!-- Table -->
      <div class="flex-1 overflow-y-auto scrollbar-thin">
        <table class="w-full text-xs border-collapse">
          <thead>
            <tr style="background:var(--muted)">
              <th class="text-left px-4 py-2.5 font-semibold" style="color:var(--muted-foreground)">#</th>
              <th class="text-right px-4 py-2.5 font-semibold" style="color:var(--muted-foreground)">AMOUNT IN RS.</th>
              <th class="text-right px-4 py-2.5 font-semibold" style="color:var(--muted-foreground)">NO. OF BILLS</th>
            </tr>
          </thead>
          <tbody>
            <!-- Bill Type section -->
            <tr v-for="row in billRows" :key="row.label" class="border-b" style="border-color:var(--border)">
              <td class="px-4 py-2" style="color:var(--foreground)">{{ row.label }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ fmt(row.amount) }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ row.bills }}</td>
            </tr>
            <!-- Bill Total row -->
            <tr class="border-b font-bold" style="border-color:var(--border);background:var(--muted)">
              <td class="px-4 py-2" style="color:var(--foreground)">Total</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ fmt(billTotal.amount) }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ billTotal.bills }}</td>
            </tr>

            <!-- Spacer -->
            <tr><td colspan="3" class="py-1" /></tr>

            <!-- Sales Type section -->
            <tr v-for="row in salesRows" :key="row.label" class="border-b" style="border-color:var(--border)">
              <td class="px-4 py-2" style="color:var(--foreground)">{{ row.label }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ fmt(row.amount) }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ row.bills }}</td>
            </tr>
            <!-- Sales Total row -->
            <tr class="border-b font-bold" style="border-color:var(--border);background:var(--muted)">
              <td class="px-4 py-2" style="color:var(--foreground)">Total</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ fmt(salesTotal.amount) }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ salesTotal.bills }}</td>
            </tr>

            <!-- Spacer -->
            <tr><td colspan="3" class="py-1" /></tr>

            <!-- Order Type section -->
            <tr v-for="row in orderTypeRows" :key="row.label" class="border-b" style="border-color:var(--border)">
              <td class="px-4 py-2" style="color:var(--foreground)">{{ row.label }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ fmt(row.amount) }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ row.bills }}</td>
            </tr>
            <!-- Order Type Total row -->
            <tr class="font-bold" style="background:var(--muted)">
              <td class="px-4 py-2" style="color:var(--foreground)">Total</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ fmt(orderTypeTotal.amount) }}</td>
              <td class="px-4 py-2 text-right tabular-nums" style="color:var(--foreground)">{{ orderTypeTotal.bills }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="px-5 py-3 border-t flex-shrink-0" style="border-color:var(--border)">
        <button
          @click="$emit('close')"
          class="w-full px-4 py-2 rounded-lg border text-sm font-medium hover:bg-muted transition-colors"
          style="border-color:var(--border);color:var(--foreground)"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { X as XIcon } from '@lucide/vue';

defineEmits(['close']);

const now = new Date();
const pad = n => String(n).padStart(2, '0');
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const todayLabel = computed(() =>
  `${pad(now.getDate())}-${months[now.getMonth()]}-${now.getFullYear()}`
);

function fmt(val) {
  return Number(val).toFixed(2);
}

// ── Dummy data matching screenshot ──────────────────────────────
const billRows = [
  { label: 'Cash Bill',   amount: 2054.00, bills: 2 },
  { label: 'Credit Bill', amount: 0.00,    bills: 0 },
  { label: 'Guest Bill',  amount: 0.00,    bills: 0 },
];
const billTotal = computed(() => ({
  amount: billRows.reduce((s, r) => s + r.amount, 0),
  bills:  billRows.reduce((s, r) => s + r.bills,  0),
}));

const salesRows = [
  { label: 'Cash Sales',   amount: 1129.00, bills: 1 },
  { label: 'Card Sales',   amount: 389.00,  bills: 1 },
  { label: 'Other Sales',  amount: 0.00,    bills: 0 },
];
const salesTotal = computed(() => ({
  amount: salesRows.reduce((s, r) => s + r.amount, 0),
  bills:  salesRows.reduce((s, r) => s + r.bills,  0),
}));

const orderTypeRows = [
  { label: 'Dine In',      amount: 925.00,  bills: 2 },
  { label: 'Take Away',    amount: 1129.00, bills: 1 },
  { label: 'Delivery',     amount: 0.00,    bills: 0 },
  { label: 'Room Service', amount: 0.00,    bills: 0 },
  { label: 'Bar Service',  amount: 0.00,    bills: 0 },
];
const orderTypeTotal = computed(() => ({
  amount: orderTypeRows.reduce((s, r) => s + r.amount, 0),
  bills:  orderTypeRows.reduce((s, r) => s + r.bills,  0),
}));
</script>
