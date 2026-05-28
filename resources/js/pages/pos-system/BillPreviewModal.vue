<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 fade-in">
    <div class="border rounded-2xl shadow-modal w-full max-w-sm max-h-[90vh] overflow-hidden flex flex-col slide-up" style="background:var(--card);border-color:var(--border)">
      <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color:var(--border)">
        <h3 class="font-semibold" style="color:var(--foreground)">Bill Preview</h3>
        <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><XIcon :size="16" /></button>
      </div>

      <div class="flex-1 overflow-y-auto scrollbar-thin p-5">
        <!-- Restaurant header -->
        <div class="text-center mb-4 pb-4 border-b border-dashed" style="border-color:var(--border)">
          <p class="font-bold text-base" style="color:var(--foreground)">DAK TRADING INDIA PVT.LTD.</p>
          <p class="text-xs font-semibold" style="color:var(--muted-foreground)">AYYAN MULTI-CUISINE RESTAURANT</p>
          <p class="text-xs" style="color:var(--muted-foreground)">123 Main Street, New York, NY 10001</p>
          <p class="text-xs" style="color:var(--muted-foreground)">Tel: +1 555-0100 | GST: 29ABCDE1234F1Z5</p>
        </div>

        <!-- Bill meta -->
        <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
          <div><span style="color:var(--muted-foreground)">Bill No:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ billNo }}</span></div>
          <div><span style="color:var(--muted-foreground)">Date:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ dateStr }}</span></div>
          <div><span style="color:var(--muted-foreground)">Time:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ timeStr }}</span></div>
          <div><span style="color:var(--muted-foreground)">Order Type:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ billType }}</span></div>
          <div><span style="color:var(--muted-foreground)">Customer:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ customerName }}</span></div>
          <div v-if="customerPhone"><span style="color:var(--muted-foreground)">Phone:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ customerPhone }}</span></div>
          <div v-if="billType === 'Dine In'">
            <span style="color:var(--muted-foreground)">Table:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ tableLabel }}</span>
          </div>
          <div v-if="billType === 'Dine In' && waiterName">
            <span style="color:var(--muted-foreground)">Waiter:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ waiterName }}</span>
          </div>
          <div v-if="billType === 'Dine In' && covers > 0">
            <span style="color:var(--muted-foreground)">Covers:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ covers }}</span>
          </div>
        </div>

        <!-- Items table -->
        <table class="w-full text-xs mb-4">
          <thead>
            <tr class="border-b border-dashed" style="border-color:var(--border)">
              <th class="text-left py-1.5 font-semibold w-6" style="color:var(--muted-foreground)">#</th>
              <th class="text-left py-1.5 font-semibold" style="color:var(--muted-foreground)">Item</th>
              <th class="text-center py-1.5 font-semibold" style="color:var(--muted-foreground)">Qty</th>
              <th class="text-right py-1.5 font-semibold" style="color:var(--muted-foreground)">Rate</th>
              <th class="text-right py-1.5 font-semibold" style="color:var(--muted-foreground)">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(ci, idx) in cartItems" :key="ci.menuItem.id" class="border-b" style="border-color:var(--border)">
              <td class="py-1.5" style="color:var(--muted-foreground)">{{ idx + 1 }}</td>
              <td class="py-1.5" style="color:var(--foreground)">{{ ci.menuItem.name }}</td>
              <td class="py-1.5 text-center" style="color:var(--foreground)">{{ ci.quantity }}</td>
              <td class="py-1.5 text-right tabular-nums" style="color:var(--foreground)">${{ ci.menuItem.price.toFixed(2) }}</td>
              <td class="py-1.5 text-right font-semibold tabular-nums" style="color:var(--foreground)">${{ (ci.menuItem.price * ci.quantity).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Totals -->
        <div class="space-y-1.5 text-xs border-t border-dashed pt-3" style="border-color:var(--border)">
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Total Items</span><span class="tabular-nums" style="color:var(--foreground)">{{ itemCount }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Sub Total</span><span class="tabular-nums" style="color:var(--foreground)">${{ subtotal.toFixed(2) }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Discount</span><span class="tabular-nums" style="color:var(--foreground)">${{ discount.toFixed(2) }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Tax (8%)</span><span class="tabular-nums" style="color:var(--foreground)">${{ taxAmount.toFixed(2) }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Total</span><span class="tabular-nums" style="color:var(--foreground)">${{ total.toFixed(2) }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Round Off</span><span class="tabular-nums" style="color:var(--foreground)">${{ roundOff.toFixed(2) }}</span></div>
          <div class="flex justify-between pt-2 border-t" style="border-color:var(--border)">
            <span class="font-bold text-sm" style="color:var(--foreground)">Net Payable</span>
            <span class="font-bold text-base tabular-nums" style="color:var(--primary)">${{ netPayable.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Payment details -->
        <div class="space-y-1.5 text-xs border-t border-dashed pt-3 mt-3" style="border-color:var(--border)">
          <p class="font-semibold text-xs uppercase" style="color:var(--muted-foreground)">Payment</p>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Bill Type</span><span style="color:var(--foreground)">{{ billPayType }}</span></div>
          <div v-if="cashAmt > 0" class="flex justify-between"><span style="color:var(--muted-foreground)">Cash</span><span class="tabular-nums" style="color:var(--foreground)">${{ Number(cashAmt).toFixed(2) }}</span></div>
          <div v-if="cardAmt > 0" class="flex justify-between">
            <span style="color:var(--muted-foreground)">Card{{ cardRef ? ` (${cardRef})` : '' }}</span>
            <span class="tabular-nums" style="color:var(--foreground)">${{ Number(cardAmt).toFixed(2) }}</span>
          </div>
          <div v-if="othersAmt > 0" class="flex justify-between">
            <span style="color:var(--muted-foreground)">Others{{ othersType ? ` (${othersType})` : '' }}</span>
            <span class="tabular-nums" style="color:var(--foreground)">${{ Number(othersAmt).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between font-semibold">
            <span style="color:var(--muted-foreground)">Balance</span>
            <span class="tabular-nums" style="color:var(--foreground)">${{ balance.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Order notes -->
        <div v-if="orderNotes" class="text-xs mt-3 pt-3 border-t border-dashed" style="border-color:var(--border)">
          <span class="font-semibold" style="color:var(--muted-foreground)">Note: </span>
          <span style="color:var(--foreground)">{{ orderNotes }}</span>
        </div>

        <p class="text-center text-xs mt-4 pt-3 border-t border-dashed" style="color:var(--muted-foreground);border-color:var(--border)">Thank you for dining with us!</p>
      </div>

      <div class="px-5 pb-5 pt-3 flex gap-2 border-t flex-shrink-0" style="border-color:var(--border)">
        <button @click="$emit('close')" class="flex-1 px-4 py-2.5 rounded-lg border text-sm font-medium hover:bg-muted transition-colors" style="border-color:var(--border);color:var(--foreground)">Close</button>
        <button class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg gradient-primary text-white text-sm font-semibold hover:opacity-90 transition-all active:scale-[0.98]" @click="doPrint"><PrinterIcon :size="14" /> Print</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { X as XIcon, Printer as PrinterIcon } from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { customers, tables, waiters } from '@/utils/mockData.js';

const props = defineProps({ order: { type: Object, default: null } });
defineEmits(['close']);
const posStore = usePOSStore();

const now     = new Date();
const pad     = n => String(n).padStart(2, '0');
const billNo  = computed(() => props.order ? props.order.id : `BILL-${2854 + Math.floor(Math.random() * 10)}`);
const dateStr = `${pad(now.getDate())}/${pad(now.getMonth() + 1)}/${now.getFullYear()}`;
const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}`;

// When opened from ongoing list use order snapshot, otherwise use live store
const cartItems  = computed(() => props.order ? props.order.cartItems  : posStore.cartItems);
const billType   = computed(() => props.order ? props.order.orderType  : posStore.billType);
const covers     = computed(() => props.order ? props.order.covers     : posStore.covers);
const discount   = computed(() => props.order ? props.order.discount   : posStore.discount);
const subtotal   = computed(() => props.order ? props.order.subtotal   : posStore.subtotal);
const taxAmount  = computed(() => props.order ? props.order.taxAmount  : posStore.taxAmount);
const total      = computed(() => props.order ? props.order.total      : posStore.total);
const roundOff   = computed(() => props.order ? props.order.roundOff   : posStore.roundOff);
const netPayable = computed(() => props.order ? props.order.netPayable : posStore.netPayable);
const itemCount  = computed(() => props.order ? props.order.itemCount  : posStore.itemCount);
const billPayType = computed(() => props.order ? props.order.billPayType : posStore.billPayType);
const cashAmt    = computed(() => props.order ? props.order.cashAmt   : posStore.cashAmt);
const cardAmt    = computed(() => props.order ? props.order.cardAmt   : posStore.cardAmt);
const cardRef    = computed(() => props.order ? props.order.cardRef   : posStore.cardRef);
const othersAmt  = computed(() => props.order ? props.order.othersAmt : posStore.othersAmt);
const othersType = computed(() => props.order ? props.order.othersType : posStore.othersType);
const orderNotes = computed(() => props.order ? props.order.notes     : posStore.notes);
const balance    = computed(() => netPayable.value - (cashAmt.value + cardAmt.value + (props.order ? props.order.othersAmt : posStore.othersAmt)));

const customerName  = computed(() => props.order ? props.order.customerName  : (customers.find(c => c.id === posStore.customerId)?.name || 'Walk-In Customer'));
const customerPhone = computed(() => props.order ? props.order.customerPhone : (customers.find(c => c.id === posStore.customerId)?.phone || ''));
const tableLabel    = computed(() => {
  if (props.order) return props.order.tableLabel !== '—' ? `Table ${props.order.tableLabel?.replace('T','')}` : '—';
  const t = tables.find(t => t.id === posStore.tableId);
  return t ? `Table ${t.number}` : '—';
});
const waiterName = computed(() => {
  if (props.order) return props.order.waiterName || '';
  const w = waiters.find(w => w.id === posStore.waiterId);
  return w ? w.name : '';
});

function doPrint() { window.print(); }
</script>
