<template>
  <div class="ongoing-root">

    <!-- Empty state -->
    <div v-if="orders.length === 0" class="ongoing-empty">
      <div class="empty-icon"><ClipboardListIcon :size="32" /></div>
      <p class="empty-t">No ongoing orders</p>
      <p class="empty-s">Saved orders will appear here</p>
    </div>

    <!-- Order cards grid -->
    <div v-else class="ongoing-grid">
      <div
        v-for="order in orders"
        :key="order.id"
        class="order-card"
        :class="{ 'card--cancelled': order.status === 'Cancelled' }"
      >
        <!-- Card header -->
        <div class="card-head">
          <div>
            <p class="card-order-no">Order : {{ order.id }}</p>
            <p class="card-meta">Customer : {{ order.customerName }}</p>
            <p v-if="order.customerPhone" class="card-meta">Mobile : {{ order.customerPhone }}</p>
            <p class="card-meta">
              Table :
              <span class="card-table">{{ order.tableLabel !== '—' ? order.tableLabel : '—' }}</span>
            </p>
            <p class="card-meta">Total Items : {{ order.itemCount }}</p>
            <p class="card-amount">Amount to Pay : <strong>${{ order.netPayable.toFixed(2) }}</strong></p>
          </div>
          <div class="card-type-badge" :class="typeBadgeClass(order.orderType)">
            {{ order.orderType }}
          </div>
        </div>

        <!-- Status + time row -->
        <div class="card-status-row">
          <span class="status-chip" :class="statusClass(order.status)">{{ order.status }}</span>
          <span class="card-time">{{ order.createdAt }}</span>
        </div>

        <!-- Action buttons -->
        <div class="card-actions">
          <!-- KOT -->
          <button
            class="act-btn act-btn--icon"
            title="KOT"
            :disabled="order.status === 'Cancelled' || order.status === 'Completed'"
            @click="$emit('kot', order)"
          >
            <FileTextIcon :size="13" />
          </button>

          <!-- Bill Preview -->
          <button
            class="act-btn act-btn--icon"
            title="Bill Preview"
            :disabled="order.status === 'Cancelled'"
            @click="$emit('bill', order)"
          >
            <PrinterIcon :size="13" />
          </button>

          <!-- Edit / Resume -->
          <button
            class="act-btn act-btn--icon"
            title="Edit Order"
            :disabled="order.status === 'Cancelled' || order.status === 'Completed'"
            @click="handleResume(order)"
          >
            <PencilIcon :size="13" />
          </button>

          <!-- Complete -->
          <button
            v-if="order.status !== 'Completed' && order.status !== 'Cancelled'"
            class="act-btn act-btn--complete"
            @click="$emit('complete', order.id)"
          >
            Complete
          </button>

          <!-- Status badge when done -->
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
  ClipboardList as ClipboardListIcon,
  FileText as FileTextIcon,
  Printer as PrinterIcon,
  Pencil as PencilIcon,
} from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';

const emit = defineEmits(['resume', 'kot', 'bill', 'complete']);
const posStore = usePOSStore();

const props = defineProps({
  orders: { type: Array, default: () => [] },
});

function handleResume(order) {
  posStore.resumeOrder(order.id);
  emit('resume');
}

function statusClass(status) {
  return {
    'status--pending':   status === 'Pending',
    'status--preparing': status === 'Preparing',
    'status--ready':     status === 'Ready',
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
.ongoing-root {
  padding: 14px 16px;
  height: 100%;
  overflow-y: auto;
}

/* Empty */
.ongoing-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  gap: 8px;
  text-align: center;
}
.empty-icon {
  width: 60px; height: 60px;
  border-radius: 14px;
  border: 2px dashed var(--border);
  display: flex; align-items: center; justify-content: center;
  color: var(--muted-foreground);
}
.empty-t { font-size: 13px; font-weight: 700; color: var(--foreground); margin: 0; }
.empty-s { font-size: 11px; color: var(--muted-foreground); margin: 0; }

/* Grid */
.ongoing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 12px;
}

/* Card */
.order-card {
  border: 1px solid var(--border);
  border-radius: 10px;
  background: var(--card);
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: border-color .15s, box-shadow .15s;
}
.order-card:hover { border-color: #9ca3af; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.card--cancelled { opacity: .6; }

/* Card head */
.card-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
}
.card-order-no {
  font-size: 12px; font-weight: 700;
  color: var(--foreground); margin: 0 0 4px;
}
.card-meta {
  font-size: 11px; color: var(--muted-foreground); margin: 2px 0 0;
}
.card-table { color: #E85D26; font-weight: 700; }
.card-amount {
  font-size: 12px; color: var(--foreground); margin: 6px 0 0;
}
.card-amount strong { color: #E85D26; }

/* Type badge */
.card-type-badge {
  font-size: 10px; font-weight: 700;
  padding: 3px 8px; border-radius: 6px;
  white-space: nowrap; flex-shrink: 0;
}
.type--dinein   { background: #dbeafe; color: #1d4ed8; }
.type--takeaway { background: #fef9c3; color: #854d0e; }
.type--delivery { background: #dcfce7; color: #166534; }

/* Status row */
.card-status-row {
  display: flex; align-items: center; justify-content: space-between;
}
.status-chip {
  font-size: 10px; font-weight: 700;
  padding: 2px 8px; border-radius: 20px;
}
.status--pending   { background: #fef3c7; color: #92400e; }
.status--preparing { background: #dbeafe; color: #1e40af; }
.status--ready     { background: #dcfce7; color: #166534; }
.status--completed { background: #f0fdf4; color: #15803d; }
.status--cancelled { background: #fee2e2; color: #991b1b; }

.card-time { font-size: 10px; color: var(--muted-foreground); }

/* Actions */
.card-actions {
  display: flex; align-items: center; gap: 6px;
  border-top: 1px solid var(--border);
  padding-top: 8px;
}
.act-btn {
  display: flex; align-items: center; justify-content: center;
  border-radius: 6px; border: 1px solid var(--border);
  background: var(--muted); color: var(--foreground);
  cursor: pointer; transition: all .15s;
  font-family: var(--font-sans);
}
.act-btn--icon {
  width: 28px; height: 28px;
}
.act-btn--icon:hover:not(:disabled) { border-color: #9ca3af; background: var(--background); }
.act-btn:disabled { opacity: .35; cursor: not-allowed; }

.act-btn--complete {
  margin-left: auto;
  padding: 4px 12px;
  font-size: 11px; font-weight: 700;
  background: #16a34a; color: #fff;
  border-color: #16a34a;
}
.act-btn--complete:hover { background: #15803d; }

.done-chip {
  margin-left: auto;
  font-size: 10px; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
}
.done-chip--done   { background: #dcfce7; color: #166534; }
.done-chip--cancel { background: #fee2e2; color: #991b1b; }
</style>
