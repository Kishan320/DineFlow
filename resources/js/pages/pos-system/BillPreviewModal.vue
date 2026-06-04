<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 fade-in">
    <div class="border rounded-2xl shadow-modal w-full max-w-sm max-h-[90vh] overflow-hidden flex flex-col slide-up" style="background:var(--card);border-color:var(--border)">
      <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color:var(--border)">
        <h3 class="font-semibold" style="color:var(--foreground)">Bill Preview</h3>
        <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><XIcon :size="16" /></button>
      </div>

      <div class="flex-1 overflow-y-auto scrollbar-thin p-5">
        <!-- Restaurant header from settings -->
        <div class="text-center mb-4 pb-4 border-b border-dashed" style="border-color:var(--border)">
          <p class="font-bold text-base" style="color:var(--foreground)">{{ settings?.restaurant_name ?? 'Restaurant' }}</p>
          <p v-if="settings?.business_unit" class="text-xs font-semibold" style="color:var(--muted-foreground)">{{ settings.business_unit }}</p>
          <p v-if="settings?.gst_no" class="text-xs" style="color:var(--muted-foreground)">GST: {{ settings.gst_no }}</p>
        </div>

        <!-- Bill meta -->
        <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
          <div><span style="color:var(--muted-foreground)">Bill No:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ invoiceNo }}</span></div>
          <div><span style="color:var(--muted-foreground)">Date:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ dateStr }}</span></div>
          <div><span style="color:var(--muted-foreground)">Time:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ timeStr }}</span></div>
          <div><span style="color:var(--muted-foreground)">Type:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ orderType }}</span></div>
          <div><span style="color:var(--muted-foreground)">Customer:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ customerName }}</span></div>
          <div v-if="customerPhone"><span style="color:var(--muted-foreground)">Phone:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ customerPhone }}</span></div>
          <div v-if="orderType === 'Dine In' && tableLabel"><span style="color:var(--muted-foreground)">Table:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ tableLabel }}</span></div>
          <div v-if="waiterName"><span style="color:var(--muted-foreground)">Waiter:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ waiterName }}</span></div>
          <div v-if="covers > 0 && orderType === 'Dine In'"><span style="color:var(--muted-foreground)">Covers:</span><span class="font-semibold ml-1" style="color:var(--foreground)">{{ covers }}</span></div>
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
            <tr v-for="(ci, idx) in billItems" :key="idx" class="border-b" style="border-color:var(--border)">
              <td class="py-1.5" style="color:var(--muted-foreground)">{{ idx + 1 }}</td>
              <td class="py-1.5" style="color:var(--foreground)">{{ ci.name ?? ci.item_name }}</td>
              <td class="py-1.5 text-center" style="color:var(--foreground)">{{ ci.quantity }}</td>
              <td class="py-1.5 text-right tabular-nums" style="color:var(--foreground)">{{ fmt(ci.price ?? ci.unit_price) }}</td>
              <td class="py-1.5 text-right font-semibold tabular-nums" style="color:var(--foreground)">{{ fmt((ci.price ?? ci.unit_price) * ci.quantity) }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Totals -->
        <div class="space-y-1.5 text-xs border-t border-dashed pt-3" style="border-color:var(--border)">
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Sub Total</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(subtotal) }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Discount</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(discountAmt) }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Tax</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(taxAmount) }}</span></div>
          <!-- Tax breakdown -->
          <template v-if="taxBreakdown && Object.keys(taxBreakdown).length">
            <div v-for="(amt, rate) in taxBreakdown" :key="rate" class="flex justify-between pl-3">
              <span style="color:var(--muted-foreground)">@ {{ rate }}</span>
              <span class="tabular-nums" style="color:var(--foreground)">{{ fmt(amt) }}</span>
            </div>
          </template>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Total</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(total) }}</span></div>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Round Off</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(roundOff) }}</span></div>
          <div class="flex justify-between pt-2 border-t" style="border-color:var(--border)">
            <span class="font-bold text-sm" style="color:var(--foreground)">Net Payable</span>
            <span class="font-bold text-base tabular-nums" style="color:var(--primary)">{{ fmt(netPayable) }}</span>
          </div>
        </div>

        <!-- Payment details -->
        <div class="space-y-1.5 text-xs border-t border-dashed pt-3 mt-3" style="border-color:var(--border)">
          <p class="font-semibold text-xs uppercase" style="color:var(--muted-foreground)">Payment</p>
          <div class="flex justify-between"><span style="color:var(--muted-foreground)">Bill Type</span><span style="color:var(--foreground)">{{ billPayType }}</span></div>
          <div v-if="cashAmt > 0" class="flex justify-between"><span style="color:var(--muted-foreground)">Cash</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(cashAmt) }}</span></div>
          <div v-if="cardAmt > 0" class="flex justify-between"><span style="color:var(--muted-foreground)">Card{{ cardRef ? ` (${cardRef})` : '' }}</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(cardAmt) }}</span></div>
          <div v-if="upiAmt > 0" class="flex justify-between"><span style="color:var(--muted-foreground)">UPI{{ upiRef ? ` (${upiRef})` : '' }}</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(upiAmt) }}</span></div>
          <div v-if="othersAmt > 0" class="flex justify-between"><span style="color:var(--muted-foreground)">Others{{ othersType ? ` (${othersType})` : '' }}</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(othersAmt) }}</span></div>
          <div class="flex justify-between font-semibold"><span style="color:var(--muted-foreground)">Balance</span><span class="tabular-nums" style="color:var(--foreground)">{{ fmt(balance) }}</span></div>
        </div>

        <div v-if="orderNotes" class="text-xs mt-3 pt-3 border-t border-dashed" style="border-color:var(--border)">
          <span class="font-semibold" style="color:var(--muted-foreground)">Note: </span>
          <span style="color:var(--foreground)">{{ orderNotes }}</span>
        </div>

        <p v-if="settings?.bill_footer_text" class="text-center text-xs mt-4 pt-3 border-t border-dashed" style="color:var(--muted-foreground);border-color:var(--border)">{{ settings.bill_footer_text }}</p>
        <p v-else class="text-center text-xs mt-4 pt-3 border-t border-dashed" style="color:var(--muted-foreground);border-color:var(--border)">Thank you for dining with us!</p>
      </div>

      <div class="px-5 pb-5 pt-3 flex gap-2 border-t flex-shrink-0" style="border-color:var(--border)">
        <button @click="$emit('close')" class="flex-1 px-4 py-2.5 rounded-lg border text-sm font-medium hover:bg-muted transition-colors" style="border-color:var(--border);color:var(--foreground)">Close</button>
        <button class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg gradient-primary text-white text-sm font-semibold hover:opacity-90 transition-all active:scale-[0.98]" @click="doPrint">
          <PrinterIcon :size="14" /> Print
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { X as XIcon, Printer as PrinterIcon } from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { restaurantSettingsApi, posApi } from '@/services/settingsApi.js';

const props = defineProps({ order: { type: Object, default: null } });
defineEmits(['close']);

const posStore  = usePOSStore();
const settings  = ref(null);
const billData  = ref(null);

onMounted(async () => {
  try {
    const res = await restaurantSettingsApi.get();
    settings.value = res.data ?? res;
  } catch { /* optional */ }

  if (props.order?.id) {
    try {
      const res = await posApi.getBillData(props.order.id);
      billData.value = res.order;
    } catch { /* fall back to prop */ }
  }
});

const now      = new Date();
const pad      = n => String(n).padStart(2, '0');
const dateStr  = `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()}`;
const timeStr  = `${pad(now.getHours())}:${pad(now.getMinutes())}`;

const src = computed(() => billData.value ?? props.order ?? null);

const invoiceNo   = computed(() => src.value?.invoice_no ?? src.value?.order_no ?? 'PREVIEW');
const orderType   = computed(() => src.value?.order_type ?? posStore.billType);
const covers      = computed(() => src.value?.covers ?? posStore.covers ?? 0);
const discountAmt = computed(() => parseFloat(src.value?.discount ?? posStore.discountAmt ?? 0));
const subtotal    = computed(() => parseFloat(src.value?.subtotal ?? posStore.subtotal ?? 0));
const taxAmount   = computed(() => parseFloat(src.value?.tax_amount ?? posStore.taxAmount ?? 0));
const taxBreakdown = computed(() => src.value?.tax_breakdown ?? null);
const total       = computed(() => parseFloat(src.value?.total ?? posStore.total ?? 0));
const roundOff    = computed(() => parseFloat(src.value?.round_off ?? posStore.roundOff ?? 0));
const netPayable  = computed(() => parseFloat(src.value?.net_payable ?? posStore.netPayable ?? 0));
const billPayType = computed(() => src.value?.bill_pay_type ?? posStore.billPayType ?? 'Cash');
const cashAmt     = computed(() => parseFloat(src.value?.cash_amt ?? posStore.cashAmt ?? 0));
const cardAmt     = computed(() => parseFloat(src.value?.card_amt ?? posStore.cardAmt ?? 0));
const cardRef     = computed(() => src.value?.card_ref ?? posStore.cardRef ?? '');
const upiAmt      = computed(() => parseFloat(src.value?.upi_amt ?? posStore.upiAmt ?? 0));
const upiRef      = computed(() => src.value?.upi_ref ?? posStore.upiRef ?? '');
const othersAmt   = computed(() => parseFloat(src.value?.others_amt ?? posStore.othersAmt ?? 0));
const othersType  = computed(() => src.value?.others_type ?? posStore.othersType ?? '');
const orderNotes  = computed(() => src.value?.notes ?? posStore.notes ?? '');
const balance     = computed(() => netPayable.value - (cashAmt.value + cardAmt.value + upiAmt.value + othersAmt.value));

const customerName  = computed(() => src.value?.customer_name ?? posStore.customerObj?.name ?? 'Walk-In Guest');
const customerPhone = computed(() => src.value?.customer_phone ?? posStore.customerObj?.mobile ?? '');
const tableLabel    = computed(() => src.value?.table_label ?? posStore.tables.find(t => t.id === posStore.tableId)?.table_name ?? '');
const waiterName    = computed(() => src.value?.waiter_name ?? posStore.waiters.find(w => w.id === posStore.waiterId)?.name ?? '');

const billItems = computed(() => {
  if (src.value?.items?.length) return src.value.items;
  return posStore.cartItems.map(ci => ({
    name:      ci.name,
    quantity:  ci.quantity,
    price:     ci.price,
    unit_price: ci.price,
  }));
});

function fmt(val) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val ?? 0);
}
function doPrint() { window.print(); }
</script>
