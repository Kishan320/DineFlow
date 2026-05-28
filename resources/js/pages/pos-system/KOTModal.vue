<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 fade-in">
    <div class="border rounded-2xl shadow-modal w-full max-w-md slide-up" style="background:var(--card);border-color:var(--border)">
      <!-- Modal Header -->
      <div class="flex items-center justify-between px-5 py-3 border-b" style="border-color:var(--border)">
        <h3 class="font-bold text-base" style="color:var(--foreground)">KOT Items</h3>
        <div class="flex items-center gap-2">
          <button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg gradient-primary text-white text-xs font-semibold hover:opacity-90 transition-all" @click="doPrint">
            <PrinterIcon :size="13" /> Print
          </button>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="16" />
          </button>
        </div>
      </div>

      <!-- KOT Body -->
      <div class="px-6 py-5">
        <!-- Restaurant Info -->
        <div class="text-center mb-4">
          <p class="font-bold text-sm uppercase tracking-wide" style="color:var(--foreground)">DAK TRADING INDIA PVT.LTD.</p>
          <p class="text-xs" style="color:var(--muted-foreground)">AYYAN MULTI-CUISINE RESTAURANT</p>
        </div>

        <!-- KOT Meta -->
        <div class="flex justify-between text-xs mb-3">
          <span style="color:var(--foreground)">Order No: <strong>{{ orderNo }}</strong></span>
          <span style="color:var(--foreground)">{{ orderType }}</span>
        </div>

        <!-- Customer & Table -->
        <div class="flex justify-between text-xs mb-4">
          <span style="color:var(--foreground)">{{ customerName }}</span>
          <span style="color:var(--foreground)">Table: {{ tableLabel }}</span>
        </div>

        <!-- KOT groups -->
        <div v-for="kot in kots" :key="kot.kotNo">
          <!-- Items Table -->
          <table class="w-full text-xs border-collapse">
            <thead>
              <tr class="border-t border-b" style="border-color:var(--border)">
                <th class="py-1.5 text-left font-semibold w-16" style="color:var(--foreground)">KOT No</th>
                <th class="py-1.5 text-left font-semibold" style="color:var(--foreground)">Item</th>
                <th class="py-1.5 text-right font-semibold w-10" style="color:var(--foreground)">Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(ci, idx) in kot.items" :key="ci.menuItem?.id ?? idx" class="border-b" style="border-color:var(--border)">
                <td class="py-1.5 align-top" style="color:var(--muted-foreground)">
                  <span v-if="idx === 0">{{ kot.kotNo }}<br /><span style="font-size:9px">{{ kot.timestamp }}</span></span>
                </td>
                <td class="py-1.5" style="color:var(--foreground)">{{ ci.menuItem?.name ?? ci.name }}</td>
                <td class="py-1.5 text-right font-semibold" style="color:var(--foreground)">{{ ci.quantity }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Special Notes -->
        <div v-if="orderNotes" class="mt-4 border rounded-lg px-3 py-2" style="background:color-mix(in srgb, var(--warning) 10%, transparent);border-color:color-mix(in srgb, var(--warning) 20%, transparent)">
          <p class="text-xs font-semibold mb-0.5" style="color:var(--warning)">Special Notes</p>
          <p class="text-xs" style="color:var(--foreground)">{{ orderNotes }}</p>
        </div>
      </div>

      <!-- Actions -->
      <div class="px-5 pb-5 flex gap-2">
        <button @click="$emit('close')" class="flex-1 px-4 py-2.5 rounded-lg border text-sm font-medium hover:bg-muted transition-colors" style="border-color:var(--border);color:var(--foreground)">Close</button>
        <button class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg gradient-primary text-white text-sm font-semibold hover:opacity-90 transition-all active:scale-[0.98]" @click="doPrint">
          <PrinterIcon :size="14" /> Print KOT
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { X as XIcon, Printer as PrinterIcon } from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { customers, tables } from '@/utils/mockData.js';

const props = defineProps({ order: { type: Object, default: null } });
defineEmits(['close']);
const posStore = usePOSStore();

const now = new Date();
const pad = n => String(n).padStart(2, '0');
const dateTimeStr = `${pad(now.getDate())}-${pad(now.getMonth()+1)}-${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

// Use order snapshot when opened from ongoing list, else live store
const cartItems   = computed(() => props.order ? props.order.cartItems  : posStore.cartItems);
const orderType   = computed(() => props.order ? props.order.orderType  : posStore.billType);
const orderNotes  = computed(() => props.order ? props.order.notes      : posStore.notes);

const orderNo = computed(() => props.order ? props.order.id : `${String(Math.floor(Math.random() * 90000) + 100000)}-${String(Math.floor(Math.random() * 900) + 100)}`);

// KOT history from order, or generate a new one
const kots = computed(() => {
  if (props.order?.kots?.length) return props.order.kots;
  return [{
    kotNo: `0${String(Math.floor(Math.random() * 900) + 100)}`,
    timestamp: dateTimeStr,
    items: cartItems.value,
  }];
});

const customerName = computed(() => props.order ? props.order.customerName : (customers.find(c => c.id === posStore.customerId)?.name || 'Walk-In Customer'));
const tableLabel   = computed(() => {
  if (props.order) return props.order.tableLabel !== '—' ? props.order.tableLabel : '—';
  const t = tables.find(t => t.id === posStore.tableId);
  return t ? `T${t.number}` : '—';
});

function doPrint() { window.print(); }
</script>
