<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 fade-in">
    <div class="border rounded-2xl shadow-modal w-full max-w-md slide-up" style="background:var(--card);border-color:var(--border)">

      <div class="flex items-center justify-between px-5 py-3 border-b" style="border-color:var(--border)">
        <h3 class="font-bold text-base" style="color:var(--foreground)">KOT</h3>
        <div class="flex items-center gap-2">
          <button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg gradient-primary text-white text-xs font-semibold hover:opacity-90 transition-all" @click="handleKot">
            <PrinterIcon :size="13" /> {{ submitting ? 'Sending...' : 'Send to Kitchen' }}
          </button>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="16" />
          </button>
        </div>
      </div>

      <div class="px-6 py-5">
        <!-- Restaurant info from settings -->
        <div class="text-center mb-4">
          <p class="font-bold text-sm uppercase tracking-wide" style="color:var(--foreground)">{{ settings?.restaurant_name ?? 'Restaurant' }}</p>
          <p v-if="settings?.gst_no" class="text-xs" style="color:var(--muted-foreground)">GST: {{ settings.gst_no }}</p>
        </div>

        <!-- KOT Meta -->
        <div class="flex justify-between text-xs mb-3">
          <span style="color:var(--foreground)">Order: <strong>{{ orderNo }}</strong></span>
          <span style="color:var(--foreground)">{{ orderType }}</span>
        </div>
        <div class="flex justify-between text-xs mb-4">
          <span style="color:var(--foreground)">{{ customerName }}</span>
          <span v-if="tableLabel" style="color:var(--foreground)">Table: {{ tableLabel }}</span>
        </div>

        <!-- Live cart items (for new KOT) or historical KOTs -->
        <table class="w-full text-xs border-collapse">
          <thead>
            <tr class="border-t border-b" style="border-color:var(--border)">
              <th class="py-1.5 text-left font-semibold w-16" style="color:var(--foreground)">KOT No</th>
              <th class="py-1.5 text-left font-semibold" style="color:var(--foreground)">Item</th>
              <th class="py-1.5 text-right font-semibold w-10" style="color:var(--foreground)">Qty</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(ci, idx) in displayItems" :key="idx" class="border-b" style="border-color:var(--border)">
              <td class="py-1.5 align-top" style="color:var(--muted-foreground)">
                <span v-if="idx === 0">{{ kotLabel }}<br /><span style="font-size:9px">{{ nowStr }}</span></span>
              </td>
              <td class="py-1.5" style="color:var(--foreground)">{{ ci.name ?? ci.item_name }}</td>
              <td class="py-1.5 text-right font-semibold" style="color:var(--foreground)">{{ ci.quantity }}</td>
            </tr>
          </tbody>
        </table>

        <div v-if="orderNotes" class="mt-4 border rounded-lg px-3 py-2" style="background:color-mix(in srgb, var(--warning) 10%, transparent);border-color:color-mix(in srgb, var(--warning) 20%, transparent)">
          <p class="text-xs font-semibold mb-0.5" style="color:var(--warning)">Special Notes</p>
          <p class="text-xs" style="color:var(--foreground)">{{ orderNotes }}</p>
        </div>
      </div>

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
import { ref, computed, onMounted } from 'vue';
import { X as XIcon, Printer as PrinterIcon } from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { restaurantSettingsApi } from '@/services/settingsApi.js';

const props = defineProps({ order: { type: Object, default: null } });
const emit  = defineEmits(['close']);
const posStore = usePOSStore();

const settings   = ref(null);
const submitting = ref(false);

onMounted(async () => {
  try {
    const res = await restaurantSettingsApi.get();
    settings.value = res.data ?? res;
  } catch { /* settings optional */ }
});

const now    = new Date();
const pad    = n => String(n).padStart(2, '0');
const nowStr = `${pad(now.getDate())}-${pad(now.getMonth()+1)}-${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

const orderNo     = computed(() => props.order?.order_no ?? posStore.currentOrderId ?? 'NEW');
const orderType   = computed(() => props.order?.order_type ?? posStore.billType);
const orderNotes  = computed(() => props.order?.notes ?? posStore.notes);
const customerName = computed(() => props.order?.customer_name ?? posStore.customerObj?.name ?? 'Walk-In Guest');
const tableLabel  = computed(() => props.order?.table_label ?? posStore.tables.find(t => t.id === posStore.tableId)?.table_name ?? null);

const kotLabel = computed(() => {
  if (props.order?.kots?.length) return `#${props.order.kots.length + 1}`;
  return '#NEW';
});

// Items to display
const displayItems = computed(() => {
  if (props.order) {
    // show last KOT items if any, else order items
    if (props.order.kots?.length) {
      const last = props.order.kots[props.order.kots.length - 1];
      return last.items ?? [];
    }
    return props.order.items ?? [];
  }
  return posStore.cartItems.map(ci => ({ name: ci.name, quantity: ci.quantity }));
});

async function handleKot() {
  if (submitting.value) return;
  submitting.value = true;
  try {
    if (props.order) {
      // Generate KOT for existing order
      const kotItems = (props.order.items ?? []).map(i => ({
        item_id:   i.item_id,
        item_name: i.item_name,
        quantity:  i.quantity,
      }));
      await posStore.generateKot(props.order.id, kotItems, props.order.notes);
    } else if (posStore.currentOrderId) {
      const kotItems = posStore.cartItems.map(ci => ({
        item_id:   ci.item_id,
        item_name: ci.name,
        quantity:  ci.quantity,
      }));
      await posStore.generateKot(posStore.currentOrderId, kotItems, posStore.notes);
    }
    emit('close');
  } finally {
    submitting.value = false;
  }
}

function doPrint() { window.print(); }
</script>
