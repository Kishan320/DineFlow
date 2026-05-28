<template>
  <div class="summary-root">

    <!-- Header -->
    <div class="summary-header">
      <div class="summary-header-icon">
        <ReceiptTextIcon :size="16" />
      </div>
      <div>
        <p class="summary-title">Order Summary</p>
        <p class="summary-sub">{{ posStore.itemCount }} item{{ posStore.itemCount !== 1 ? 's' : '' }} selected</p>
      </div>
    </div>

    <!-- Totals card -->
    <div class="totals-card">
      <div class="total-row">
        <span class="total-label">Subtotal</span>
        <span class="total-val">${{ posStore.subtotal.toFixed(2) }}</span>
      </div>
      <div class="total-row">
        <span class="total-label">Tax <span class="total-label-note">(8%)</span></span>
        <span class="total-val">${{ posStore.taxAmount.toFixed(2) }}</span>
      </div>
      <div v-if="posStore.discount > 0" class="total-row total-row--discount">
        <span class="total-label" style="color:var(--success)">Discount</span>
        <span class="total-val" style="color:var(--success)">-${{ posStore.discount.toFixed(2) }}</span>
      </div>
      <div class="total-row total-row--grand">
        <span class="grand-label">Grand Total</span>
        <span class="grand-val">${{ posStore.total.toFixed(2) }}</span>
      </div>
    </div>

    <!-- Payment method -->
    <div class="payment-section">
      <p class="section-heading">Payment Method</p>
      <div class="payment-list">
        <button
          v-for="pm in paymentMethods"
          :key="pm.id"
          class="payment-btn"
          :class="{ active: paymentMethod === pm.id }"
          @click="paymentMethod = pm.id"
        >
          <div class="payment-btn-icon">
            <component :is="pm.icon" :size="15" />
          </div>
          <span class="payment-btn-label">{{ pm.label }}</span>
          <div v-if="paymentMethod === pm.id" class="payment-check">
            <CheckIcon :size="11" />
          </div>
        </button>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="quick-actions">
      <button
        class="quick-btn"
        :disabled="!hasItems"
        @click="$emit('kot')"
      >
        <FileTextIcon :size="13" />
        <span>KOT</span>
      </button>
      <button
        class="quick-btn"
        :disabled="!hasItems"
        @click="$emit('bill-preview')"
      >
        <PrinterIcon :size="13" />
        <span>Bill Preview</span>
      </button>
    </div>

    <!-- Spacer -->
    <div class="summary-spacer" />

    <!-- Main CTA -->
    <div class="cta-area">
      <button
        class="place-order-btn"
        :disabled="!hasItems || isPlacing"
        @click="handlePlace"
      >
        <span v-if="isPlacing" class="spinner" />
        <ReceiptTextIcon v-else :size="16" />
        <span>{{ isPlacing ? 'Placing Order…' : 'Place Order' }}</span>
      </button>

      <div class="cta-secondary">
        <button class="secondary-btn" :disabled="!hasItems">
          <SaveIcon :size="13" />
          <span>Save Draft</span>
        </button>
        <button class="secondary-btn secondary-btn--danger" :disabled="!hasItems" @click="posStore.clearCart()">
          <XIcon :size="13" />
          <span>Cancel</span>
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
  ReceiptText as ReceiptTextIcon,
  Check as CheckIcon,
  FileText as FileTextIcon,
  Printer as PrinterIcon,
  X as XIcon,
  Save as SaveIcon,
  Banknote as BanknoteIcon,
  CreditCard as CreditCardIcon,
  Smartphone as SmartphoneIcon,
} from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';

const emit = defineEmits(['kot', 'bill-preview', 'place-order']);
const posStore = usePOSStore();

const paymentMethod = ref('pm-cash');
const isPlacing     = ref(false);
const hasItems      = computed(() => posStore.cartItems.length > 0);

const paymentMethods = [
  { id: 'pm-cash', label: 'Cash',  icon: BanknoteIcon },
  { id: 'pm-card', label: 'Card',  icon: CreditCardIcon },
  { id: 'pm-upi',  label: 'UPI',   icon: SmartphoneIcon },
];

async function handlePlace() {
  if (!hasItems.value) return;
  isPlacing.value = true;
  await new Promise(r => setTimeout(r, 800));
  isPlacing.value = false;
  emit('place-order');
}
</script>

<style scoped>
.summary-root {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: var(--card);
  border-left: 1px solid var(--border);
}

/* ── Header ── */
.summary-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 16px 12px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.summary-header-icon {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: var(--accent);
  color: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.summary-title { font-size: 14px; font-weight: 700; color: var(--foreground); }
.summary-sub   { font-size: 11px; color: var(--muted-foreground); margin-top: 1px; }

/* ── Totals ── */
.totals-card {
  margin: 12px 14px;
  padding: 14px;
  border-radius: 12px;
  background: var(--muted);
  border: 1px solid var(--border);
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.total-label {
  font-size: 12px;
  color: var(--muted-foreground);
}
.total-label-note { font-size: 10px; }
.total-val {
  font-size: 13px;
  font-weight: 600;
  color: var(--foreground);
  font-variant-numeric: tabular-nums;
}
.total-row--grand {
  padding-top: 10px;
  margin-top: 2px;
  border-top: 1px dashed var(--border);
}
.grand-label { font-size: 14px; font-weight: 700; color: var(--foreground); }
.grand-val   { font-size: 20px; font-weight: 800; color: var(--primary); font-variant-numeric: tabular-nums; }

/* ── Payment ── */
.payment-section {
  padding: 0 14px 12px;
  flex-shrink: 0;
}
.section-heading {
  font-size: 10px;
  font-weight: 700;
  color: var(--muted-foreground);
  text-transform: uppercase;
  letter-spacing: .06em;
  margin-bottom: 8px;
}
.payment-list { display: flex; flex-direction: column; gap: 6px; }
.payment-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 10px;
  border: 1.5px solid var(--border);
  background: var(--muted);
  color: var(--muted-foreground);
  cursor: pointer;
  transition: all .15s;
  font-family: var(--font-sans);
  width: 100%;
}
.payment-btn:hover { border-color: var(--primary); color: var(--foreground); }
.payment-btn.active {
  border-color: var(--primary);
  background: var(--accent);
  color: var(--primary);
}
.payment-btn-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  background: var(--background);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.payment-btn.active .payment-btn-icon { background: var(--primary); color: var(--primary-foreground); }
.payment-btn-label { flex: 1; font-size: 13px; font-weight: 600; text-align: left; }
.payment-check {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--primary);
  color: var(--primary-foreground);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ── Quick actions ── */
.quick-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  padding: 0 14px 12px;
  flex-shrink: 0;
}
.quick-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 8px 6px;
  border-radius: 9px;
  border: 1.5px solid var(--border);
  background: var(--muted);
  color: var(--foreground);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all .15s;
  font-family: var(--font-sans);
}
.quick-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); background: var(--accent); }
.quick-btn:disabled { opacity: .4; cursor: not-allowed; }

/* ── Spacer ── */
.summary-spacer { flex: 1; }

/* ── CTA ── */
.cta-area {
  padding: 12px 14px 16px;
  border-top: 1px solid var(--border);
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.place-order-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px;
  border-radius: 12px;
  background: linear-gradient(135deg, #E85D26 0%, #F07040 100%);
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: opacity .15s, transform .1s;
  font-family: var(--font-sans);
  box-shadow: 0 4px 14px rgba(232,93,38,.35);
}
.place-order-btn:hover:not(:disabled) { opacity: .9; }
.place-order-btn:active:not(:disabled) { transform: scale(.98); }
.place-order-btn:disabled { opacity: .4; cursor: not-allowed; box-shadow: none; }

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin .7s linear infinite;
  flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

.cta-secondary {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
.secondary-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 9px 6px;
  border-radius: 9px;
  border: 1.5px solid var(--border);
  background: var(--muted);
  color: var(--foreground);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all .15s;
  font-family: var(--font-sans);
}
.secondary-btn:hover:not(:disabled) { background: var(--border); }
.secondary-btn:disabled { opacity: .4; cursor: not-allowed; }
.secondary-btn--danger { color: var(--danger); border-color: color-mix(in srgb, var(--danger) 25%, transparent); }
.secondary-btn--danger:hover:not(:disabled) { background: color-mix(in srgb, var(--danger) 10%, transparent); }
</style>
