<template>
  <div class="ongoing-root">
    <div v-if="loading" class="ongoing-empty">
      <div class="spin-wrap"><div class="spinner" /></div>
      <p class="empty-t">Loading orders...</p>
    </div>

    <div v-else-if="orders.length === 0" class="ongoing-empty">
      <div class="empty-icon"><ClipboardListIcon :size="32" /></div>
      <p class="empty-t">No ongoing orders</p>
      <p class="empty-s">Saved orders will appear here</p>
    </div>

    <div v-else class="ongoing-grid">
      <div
        v-for="order in orders"
        :key="order.id"
        class="order-card"
        :class="{ 'card--cancelled': order.status === 'Cancelled' }"
      >
        <div class="card-head">
          <div>
            <p class="card-order-no">Order: {{ order.order_no }}</p>
            <p v-if="order.invoice_no" class="card-meta">Invoice: {{ order.invoice_no }}</p>
            <p class="card-meta">Customer: {{ order.customer_name ?? 'Walk-In Guest' }}</p>
            <p v-if="order.customer_phone" class="card-meta">Mobile: {{ order.customer_phone }}</p>
            <p v-if="order.table_label" class="card-meta">Table: <span class="card-table">{{ order.table_label }}</span></p>
            <p class="card-meta">Items: {{ order.items?.length ?? 0 }}</p>
            <p class="card-amount">Amount: <strong>{{ fmt(order.net_payable) }}</strong></p>
          </div>
          <div class="card-type-badge" :class="typeBadgeClass(order.order_type)">{{ order.order_type }}</div>
        </div>

        <div class="card-status-row">
          <span class="status-chip" :class="statusClass(order.status)">{{ order.status }}</span>
          <span class="card-time">{{ formatDate(order.created_at) }}</span>
        </div>

        <div class="card-actions">
          <button class="act-btn act-btn--icon" title="KOT" :disabled="['Cancelled','Completed'].includes(order.status)" @click="$emit('kot', order)">
            <FileTextIcon :size="13" />
          </button>
          <button class="act-btn act-btn--icon" title="Bill Preview" :disabled="order.status === 'Cancelled'" @click="$emit('bill', order)">
            <PrinterIcon :size="13" />
          </button>
          <button class="act-btn act-btn--icon" title="Edit" :disabled="['Cancelled','Completed'].includes(order.status)" @click="handleResume(order)">
            <PencilIcon :size="13" />
          </button>

          <template v-if="!['Completed','Cancelled'].includes(order.status)">
            <button class="act-btn act-btn--complete" @click="$emit('complete', order.id)">Complete</button>
            <button class="act-btn act-btn--cancel" @click="$emit('cancel', order.id)">Cancel</button>
          </template>
          <span v-else class="done-chip" :class="order.status === 'Cancelled' ? 'done-chip--cancel' : 'done-chip--done'">
            {{ order.status }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ClipboardList as ClipboardListIcon, FileText as FileTextIcon,
  Printer as PrinterIcon, Pencil as PencilIcon,
} from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';

const emit = defineEmits(['resume', 'kot', 'bill', 'complete', 'cancel']);
const posStore = usePOSStore();

defineProps({
  orders:  { type: Array,   default: () => [] },
  loading: { type: Boolean, default: false },
});

function handleResume(order) {
  posStore.resumeOrder(order);
  emit('resume');
}

function fmt(val) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val ?? 0);
}

function formatDate(dt) {
  if (!dt) return '';
  const d = new Date(dt);
  return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()} ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}

function statusClass(status) {
  return {
    'status--pending':   status === 'Pending',
    'status--confirmed': status === 'Confirmed',
    'status--preparing': status === 'Preparing',
    'status--ready':     status === 'Ready',
    'status--served':    status === 'Served',
    'status--completed': status === 'Completed',
    'status--cancelled': status === 'Cancelled',
  };
}
function typeBadgeClass(type) {
  return {
    'type--dinein':   type === 'Dine In',
    'type--takeaway': type === 'Takeaway',
    'type--delivery': type === 'Delivery',
  };
}
</script>

<style scoped>
.ongoing-root { padding: 14px 16px; height: 100%; overflow-y: auto; }
.ongoing-empty {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; min-height: 200px; gap: 8px; text-align: center;
}
.spin-wrap { display: flex; justify-content: center; margin-bottom: 8px; }
.spinner {
  width: 32px; height: 32px;
  border: 3px solid var(--border); border-top-color: var(--primary);
  border-radius: 50%; animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.empty-icon { width: 60px; height: 60px; border-radius: 14px; border: 2px dashed var(--border); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); }
.empty-t { font-size: 13px; font-weight: 700; color: var(--foreground); margin: 0; }
.empty-s { font-size: 11px; color: var(--muted-foreground); margin: 0; }

.ongoing-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }
.order-card {
  border: 1px solid var(--border); border-radius: 10px; background: var(--card);
  padding: 12px; display: flex; flex-direction: column; gap: 10px;
  transition: border-color .15s, box-shadow .15s;
}
.order-card:hover { border-color: #9ca3af; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.card--cancelled { opacity: .6; }

.card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
.card-order-no { font-size: 12px; font-weight: 700; color: var(--foreground); margin: 0 0 4px; }
.card-meta { font-size: 11px; color: var(--muted-foreground); margin: 2px 0 0; }
.card-table { color: #E85D26; font-weight: 700; }
.card-amount { font-size: 12px; color: var(--foreground); margin: 6px 0 0; }
.card-amount strong { color: #E85D26; }

.card-type-badge { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; white-space: nowrap; flex-shrink: 0; }
.type--dinein   { background: #dbeafe; color: #1d4ed8; }
.type--takeaway { background: #fef9c3; color: #854d0e; }
.type--delivery { background: #dcfce7; color: #166534; }

.card-status-row { display: flex; align-items: center; justify-content: space-between; }
.status-chip { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
.status--pending   { background: #fef3c7; color: #92400e; }
.status--confirmed { background: #e0f2fe; color: #0369a1; }
.status--preparing { background: #dbeafe; color: #1e40af; }
.status--ready     { background: #dcfce7; color: #166534; }
.status--served    { background: #f0fdf4; color: #15803d; }
.status--completed { background: #f0fdf4; color: #15803d; }
.status--cancelled { background: #fee2e2; color: #991b1b; }
.card-time { font-size: 10px; color: var(--muted-foreground); }

.card-actions { display: flex; align-items: center; gap: 6px; border-top: 1px solid var(--border); padding-top: 8px; flex-wrap: wrap; }
.act-btn {
  display: flex; align-items: center; justify-content: center;
  border-radius: 6px; border: 1px solid var(--border);
  background: var(--muted); color: var(--foreground);
  cursor: pointer; transition: all .15s; font-family: var(--font-sans);
}
.act-btn--icon { width: 28px; height: 28px; }
.act-btn--icon:hover:not(:disabled) { border-color: #9ca3af; background: var(--background); }
.act-btn:disabled { opacity: .35; cursor: not-allowed; }

.act-btn--complete { margin-left: auto; padding: 4px 10px; font-size: 11px; font-weight: 700; background: #16a34a; color: #fff; border-color: #16a34a; }
.act-btn--complete:hover { background: #15803d; }
.act-btn--cancel { padding: 4px 10px; font-size: 11px; font-weight: 700; background: #dc2626; color: #fff; border-color: #dc2626; }
.act-btn--cancel:hover { background: #b91c1c; }

.done-chip { margin-left: auto; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
.done-chip--done   { background: #dcfce7; color: #166534; }
.done-chip--cancel { background: #fee2e2; color: #991b1b; }
</style>
