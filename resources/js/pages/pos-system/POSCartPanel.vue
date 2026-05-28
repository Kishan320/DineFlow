<template>
  <div class="cart-root">

    <!-- ══ SCROLLABLE TOP AREA ══ -->
    <div class="cart-scroll scrollbar-thin">

      <!-- Customer -->
      <div class="section-block">
        <div class="section-row-between">
          <div class="section-row-left">
            <UserIcon :size="14" />
            <span class="section-title">Customer</span>
          </div>
          <button class="link-btn" @click="showPicker = !showPicker">Change</button>
        </div>

        <!-- Picker -->
        <div v-if="showPicker" class="picker-box">
          <div class="picker-search-row">
            <SearchIcon :size="13" style="color:#9ca3af;flex-shrink:0" />
            <input v-model="custSearch" placeholder="Search customer..." class="picker-inp" />
          </div>
          <div class="picker-list scrollbar-thin">
            <button
              v-for="c in filteredCustomers" :key="c.id"
              class="picker-row" :class="{ active: posStore.customerId === c.id }"
              @click="posStore.customerId = c.id; showPicker = false; custSearch = ''"
            >
              <div class="p-avatar">{{ c.name.charAt(0) }}</div>
              <div>
                <p class="p-name">{{ c.name }}</p>
                <p class="p-email">{{ c.email || 'Walk-in guest' }}</p>
              </div>
            </button>
          </div>
        </div>

        <!-- Selected -->
        <div v-else class="cust-card">
          <div class="cust-avatar">{{ selectedCustomer?.name?.charAt(0) ?? 'W' }}</div>
          <div class="cust-info">
            <p class="cust-name">{{ selectedCustomer?.name ?? 'Walk-In Guest' }}</p>
            <p v-if="selectedCustomer?.email" class="cust-meta">{{ selectedCustomer.email }}</p>
            <p v-if="selectedCustomer?.phone" class="cust-meta">{{ selectedCustomer.phone }}</p>
          </div>
        </div>
      </div>

      <!-- Order type -->
      <div class="section-block section-block--noborder">
        <div class="bill-type-row">
          <button
            v-for="t in ['Dine In','Takeaway','Delivery']" :key="t"
            class="bill-btn" :class="{ active: posStore.billType === t }"
            @click="posStore.billType = t"
          >{{ t }}</button>
        </div>

        <div v-if="posStore.billType === 'Dine In'" class="two-col" style="margin-top:8px">
          <div class="field">
            <label class="field-lbl">TABLE</label>
            <div class="sel-wrap">
              <select v-model="posStore.tableId" class="field-sel">
                <option value="">Select table</option>
                <option v-for="tb in availableTables" :key="tb.id" :value="tb.id">Table {{ tb.number }}</option>
              </select>
              <ChevronDownIcon :size="12" class="sel-arr" />
            </div>
          </div>
          <div class="field">
            <label class="field-lbl">WAITER</label>
            <div class="sel-wrap">
              <select v-model="posStore.waiterId" class="field-sel">
                <option value="">Select waiter</option>
                <option v-for="w in availableWaiters" :key="w.id" :value="w.id">{{ w.name.split(' ')[0] }}</option>
              </select>
              <ChevronDownIcon :size="12" class="sel-arr" />
            </div>
          </div>
        </div>
        <!-- Covers / Pax -->
        <div v-if="posStore.billType === 'Dine In'" class="field" style="margin-top:8px">
          <label class="field-lbl">COVERS / PAX</label>
          <input v-model.number="posStore.covers" type="number" min="1" class="field-inp" />
        </div>
      </div>

      <!-- Cart heading -->
      <div class="cart-head-row">
        <div class="section-row-left">
          <ShoppingCartIcon :size="14" />
          <span class="section-title">Cart ({{ posStore.itemCount }})</span>
        </div>
        <button v-if="posStore.cartItems.length > 0" class="clear-btn" @click="posStore.clearCart()">
          <Trash2Icon :size="12" /> Clear
        </button>
      </div>

      <!-- Cart items -->
      <div class="cart-items-area">
        <!-- Empty state -->
        <div v-if="posStore.cartItems.length === 0" class="cart-empty">
          <div class="empty-icon"><PackageIcon :size="30" /></div>
          <p class="empty-t">Your cart is empty</p>
          <p class="empty-s">Add products to get started</p>
        </div>

        <!-- Items -->
        <div v-else class="cart-list">
          <div v-for="ci in posStore.cartItems" :key="ci.menuItem.id" class="cart-row">
            <!-- Thumbnail -->
            <div class="row-thumb">
              <img :src="ci.menuItem.image" :alt="ci.menuItem.name" />
            </div>
            <!-- Info -->
            <div class="row-info">
              <p class="row-name">{{ ci.menuItem.name }}</p>
              <p class="row-cat">{{ ci.menuItem.category }}</p>
              <p class="row-unit">${{ ci.menuItem.price.toFixed(2) }} × {{ ci.quantity }}</p>
            </div>
            <!-- Qty -->
            <div class="row-qty">
              <button class="q-btn" @click="posStore.updateQty(ci.menuItem.id, -1)"><MinusIcon :size="10" /></button>
              <span class="q-num">{{ ci.quantity }}</span>
              <button class="q-btn q-btn--add" @click="posStore.updateQty(ci.menuItem.id, 1)"><PlusIcon :size="10" /></button>
            </div>
            <!-- Total + remove -->
            <div class="row-right">
              <span class="row-total">${{ (ci.menuItem.price * ci.quantity).toFixed(2) }}</span>
              <button class="row-del" @click="posStore.removeItem(ci.menuItem.id)" title="Remove"><XIcon :size="11" /></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Discount + Notes -->
      <div class="section-block">
        <div class="field">
          <label class="field-lbl">DISCOUNT ($)</label>
          <input v-model.number="posStore.discount" type="number" min="0" placeholder="0.00" class="field-inp" />
        </div>
        <div class="field" style="margin-top:8px">
          <label class="field-lbl">NOTE</label>
          <input v-model="posStore.notes" type="text" placeholder="Special requests, allergies..." class="field-inp" />
        </div>
      </div>

      <!-- Bill Type + Split Payment -->
      <div class="section-block">
        <div class="field">
          <label class="field-lbl">BILL TYPE</label>
          <div class="sel-wrap">
            <select v-model="posStore.billPayType" class="field-sel">
              <option value="Cash">Cash</option>
              <option value="Card">Card</option>
              <option value="UPI">UPI</option>
              <option value="Others">Others</option>
            </select>
            <ChevronDownIcon :size="12" class="sel-arr" />
          </div>
        </div>
        <!-- Cash -->
        <div class="field" style="margin-top:8px">
          <label class="field-lbl">CASH</label>
          <input v-model.number="posStore.cashAmt" type="number" min="0" placeholder="Amount by cash" class="field-inp" />
        </div>
        <!-- Card -->
        <div class="two-col" style="margin-top:8px">
          <div class="field">
            <label class="field-lbl">CARD REF NO.</label>
            <input v-model="posStore.cardRef" type="text" placeholder="Reference No." class="field-inp" />
          </div>
          <div class="field">
            <label class="field-lbl">CARD AMT</label>
            <input v-model.number="posStore.cardAmt" type="number" min="0" placeholder="Card Amt" class="field-inp" />
          </div>
        </div>
        <!-- Others -->
        <div class="two-col" style="margin-top:8px">
          <div class="field">
            <label class="field-lbl">OTHERS TYPE / REF</label>
            <input v-model="posStore.othersType" type="text" placeholder="Type / Reference No." class="field-inp" />
          </div>
          <div class="field">
            <label class="field-lbl">OTHERS AMT</label>
            <input v-model.number="posStore.othersAmt" type="number" min="0" placeholder="Pay Amt" class="field-inp" />
          </div>
        </div>
        <!-- Payment Note -->
        <div class="field" style="margin-top:8px">
          <label class="field-lbl">PAYMENT NOTE</label>
          <textarea v-model="posStore.paymentNote" rows="2" placeholder="Payment note..." class="field-inp" style="resize:none" />
        </div>
      </div>

      <!-- Totals (inside scroll so they don't eat fixed-bottom space) -->
      <div class="section-block totals">
        <div class="t-row">
          <span class="t-lbl">Total Items:</span>
          <span class="t-val">{{ posStore.itemCount }}</span>
        </div>
        <div class="t-row">
          <span class="t-lbl">Sub Total:</span>
          <span class="t-val">${{ posStore.subtotal.toFixed(2) }}</span>
        </div>
        <div class="t-row">
          <span class="t-lbl">Discount:</span>
          <span class="t-val">${{ posStore.discount.toFixed(2) }}</span>
        </div>
        <div class="t-row">
          <span class="t-lbl">Tax (8%):</span>
          <span class="t-val">${{ posStore.taxAmount.toFixed(2) }}</span>
        </div>
        <div class="t-row">
          <span class="t-lbl">Total:</span>
          <span class="t-val">${{ posStore.total.toFixed(2) }}</span>
        </div>
        <div class="t-row">
          <span class="t-lbl">Round Off:</span>
          <span class="t-val">${{ posStore.roundOff.toFixed(2) }}</span>
        </div>
        <div class="t-row t-row--grand">
          <span class="t-grand-lbl">Net Payable:</span>
          <span class="t-grand-val">${{ posStore.netPayable.toFixed(2) }}</span>
        </div>
        <div class="t-row">
          <span class="t-lbl">Balance:</span>
          <span class="t-val">${{ posStore.balance.toFixed(2) }}</span>
        </div>
      </div>

    </div>
    <!-- ══ END SCROLLABLE ══ -->

    <!-- ══ FIXED BOTTOM ══ -->
    <div class="cart-bottom">

      <!-- Quick actions -->
      <div class="quick-row">
        <button class="q-action" :disabled="!hasItems" @click="$emit('kot')">
          <FileTextIcon :size="13" /> KOT
        </button>
        <button class="q-action" :disabled="!hasItems" @click="$emit('bill-preview')">
          <PrinterIcon :size="13" /> Bill Preview
        </button>
        <button class="q-action q-action--save" :disabled="!hasItems" @click="handleSave">
          <SaveIcon :size="13" /> {{ saveLabel }}
        </button>
        <button class="q-action q-action--danger" :disabled="!hasItems" @click="posStore.clearCart()">
          <XIcon :size="13" /> Cancel
        </button>
      </div>

      <!-- Place order + Pay Now -->
      <button class="place-btn" :disabled="!hasItems || isPlacing" @click="handlePlace">
        <span v-if="isPlacing" class="spinner" />
        <CheckCircleIcon v-else :size="16" />
        {{ isPlacing ? 'Processing…' : 'Place Order' }}
      </button>
      <button class="place-btn" style="margin-top:6px" :disabled="!hasItems" @click="handlePlace">
        Pay Now
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
  User as UserIcon, Search as SearchIcon, ShoppingCart as ShoppingCartIcon,
  Trash2 as Trash2Icon, Minus as MinusIcon, Plus as PlusIcon, X as XIcon,
  ChevronDown as ChevronDownIcon, Package as PackageIcon,
  FileText as FileTextIcon, Printer as PrinterIcon, CheckCircle as CheckCircleIcon,
  Save as SaveIcon,
} from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { customers, tables, waiters } from '@/utils/mockData.js';

const emit = defineEmits(['kot', 'bill-preview', 'place-order']);
const posStore = usePOSStore();

const showPicker = ref(false);
const custSearch = ref('');
const isPlacing  = ref(false);

const hasItems         = computed(() => posStore.cartItems.length > 0);
const selectedCustomer = computed(() => customers.find(c => c.id === posStore.customerId));
const availableTables  = computed(() => tables.filter(t => t.status === 'Available'));
const availableWaiters = computed(() => waiters.filter(w => w.status === 'Available'));
const filteredCustomers = computed(() =>
  customers.filter(c => c.name.toLowerCase().includes(custSearch.value.toLowerCase()))
);
const saveLabel = computed(() => posStore.currentOrderId ? 'Update' : 'Save');

function handleSave() {
  if (!hasItems.value) return;
  posStore.saveOngoing();
}

async function handlePlace() {
  if (!hasItems.value) return;
  isPlacing.value = true;
  await new Promise(r => setTimeout(r, 800));
  isPlacing.value = false;
  emit('place-order');
}
</script>

<style scoped>
/* ── Root: flex column, full height ── */
.cart-root {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: #fff;
  overflow: hidden;
}

/* ── Scrollable area takes all remaining space ── */
.cart-scroll {
  flex: 1;
  overflow-y: auto;
  min-height: 0;
}

/* ── Section blocks ── */
.section-block {
  padding: 14px 16px;
  border-bottom: 1px solid #f3f4f6;
}
.section-block--noborder { border-bottom: none; padding-bottom: 0; }

.section-row-between {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}
.section-row-left {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #374151;
}
.section-title { font-size: 13px; font-weight: 700; color: #111827; }
.link-btn {
  font-size: 12px;
  font-weight: 600;
  color: #E85D26;
  background: none;
  border: none;
  cursor: pointer;
  font-family: var(--font-sans);
  padding: 0;
}
.link-btn:hover { text-decoration: underline; }

/* Customer card */
.cust-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
}
.cust-avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg,#E85D26,#F07040);
  color: #fff; font-size: 14px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.cust-info { flex: 1; min-width: 0; }
.cust-name { font-size: 13px; font-weight: 700; color: #111827; margin: 0; }
.cust-meta { font-size: 11px; color: #6b7280; margin: 1px 0 0; }

/* Picker */
.picker-box { border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
.picker-search-row {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px; border-bottom: 1px solid #e5e7eb;
}
.picker-inp {
  flex: 1; border: none; outline: none;
  font-size: 12px; color: #111827; background: transparent;
  font-family: var(--font-sans);
}
.picker-list { max-height: 150px; overflow-y: auto; }
.picker-row {
  display: flex; align-items: center; gap: 8px;
  width: 100%; padding: 8px 12px;
  background: none; border: none; cursor: pointer;
  text-align: left; transition: background .1s;
  font-family: var(--font-sans);
}
.picker-row:hover { background: #f3f4f6; }
.picker-row.active { background: #fff7ed; }
.p-avatar {
  width: 28px; height: 28px; border-radius: 50%;
  background: linear-gradient(135deg,#E85D26,#F07040);
  color: #fff; font-size: 11px; font-weight: 700;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.p-name  { font-size: 12px; font-weight: 600; color: #111827; margin: 0; }
.p-email { font-size: 10px; color: #6b7280; margin: 0; }

/* Bill type */
.bill-type-row { display: flex; gap: 6px; }
.bill-btn {
  flex: 1; padding: 8px 4px;
  border-radius: 8px; border: 1.5px solid #e5e7eb;
  background: #f9fafb; color: #6b7280;
  font-size: 12px; font-weight: 600; cursor: pointer;
  transition: all .15s; font-family: var(--font-sans);
}
.bill-btn:hover { border-color: #E85D26; color: #E85D26; }
.bill-btn.active { background: #E85D26; color: #fff; border-color: #E85D26; }

/* Fields */
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.field { display: flex; flex-direction: column; gap: 3px; }
.field-lbl {
  font-size: 10px; font-weight: 700; color: #9ca3af;
  text-transform: uppercase; letter-spacing: .05em;
}
.sel-wrap { position: relative; }
.field-sel {
  width: 100%; padding: 8px 26px 8px 10px;
  border-radius: 8px; border: 1px solid #e5e7eb;
  background: #f9fafb; color: #111827;
  font-size: 12px; font-family: var(--font-sans);
  appearance: none; outline: none; cursor: pointer;
}
.field-sel:focus { border-color: #E85D26; }
.sel-arr {
  position: absolute; right: 8px; top: 50%;
  transform: translateY(-50%); color: #9ca3af; pointer-events: none;
}
.field-inp {
  padding: 8px 10px; border-radius: 8px;
  border: 1px solid #e5e7eb; background: #f9fafb;
  color: #111827; font-size: 12px;
  font-family: var(--font-sans); outline: none; width: 100%;
}
.field-inp:focus { border-color: #E85D26; }
.field-inp::placeholder { color: #9ca3af; }

/* Cart heading */
.cart-head-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px 8px;
  border-top: 1px solid #f3f4f6;
}
.clear-btn {
  display: flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 600; color: #dc2626;
  background: none; border: none; cursor: pointer;
  font-family: var(--font-sans); padding: 4px 8px;
  border-radius: 6px; transition: background .15s;
}
.clear-btn:hover { background: #fee2e2; }

/* Cart items area */
.cart-items-area { padding: 0 12px 12px; }

.cart-empty {
  display: flex; flex-direction: column; align-items: center;
  padding: 28px 16px; text-align: center; gap: 8px;
}
.empty-icon {
  width: 56px; height: 56px; border-radius: 14px;
  border: 2px dashed #e5e7eb;
  display: flex; align-items: center; justify-content: center; color: #d1d5db;
}
.empty-t { font-size: 13px; font-weight: 700; color: #374151; margin: 0; }
.empty-s { font-size: 11px; color: #9ca3af; margin: 0; }

.cart-list { display: flex; flex-direction: column; gap: 6px; }

.cart-row {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px; border-radius: 10px;
  border: 1px solid #f3f4f6; background: #fafafa;
  transition: border-color .15s;
}
.cart-row:hover { border-color: #e5e7eb; background: #fff; }

.row-thumb {
  width: 42px; height: 42px; border-radius: 8px;
  overflow: hidden; flex-shrink: 0; background: #f3f4f6;
}
.row-thumb img { width: 100%; height: 100%; object-fit: cover; }

.row-info { flex: 1; min-width: 0; }
.row-name {
  font-size: 12px; font-weight: 700; color: #111827; margin: 0;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.row-cat  { font-size: 10px; color: #9ca3af; margin: 1px 0 0; }
.row-unit { font-size: 11px; color: #6b7280; margin: 2px 0 0; font-variant-numeric: tabular-nums; }

.row-qty { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
.q-btn {
  width: 22px; height: 22px; border-radius: 6px;
  border: 1px solid #e5e7eb; background: #fff; color: #374151;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: all .1s; flex-shrink: 0;
}
.q-btn:hover { border-color: #9ca3af; }
.q-btn--add { background: #16a34a; color: #fff; border-color: #16a34a; }
.q-btn--add:hover { background: #15803d; }
.q-num {
  width: 24px; text-align: center;
  font-size: 13px; font-weight: 700; color: #111827;
  font-variant-numeric: tabular-nums;
}

.row-right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
.row-total { font-size: 13px; font-weight: 700; color: #111827; font-variant-numeric: tabular-nums; }
.row-del {
  width: 18px; height: 18px; border-radius: 4px;
  border: none; background: none; color: #d1d5db;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: all .15s;
}
.row-del:hover { background: #fee2e2; color: #dc2626; }

/* ══ FIXED BOTTOM ══ */
.cart-bottom {
  flex-shrink: 0;
  border-top: 2px solid #f3f4f6;
  background: #fff;
}

/* Totals */
.totals {
  display: flex; flex-direction: column; gap: 5px;
}
.t-row { display: flex; justify-content: space-between; align-items: center; }
.t-lbl { font-size: 12px; color: #6b7280; }
.t-val { font-size: 13px; font-weight: 600; color: #111827; font-variant-numeric: tabular-nums; }
.t-lbl--green { color: #16a34a; }
.t-val--green { color: #16a34a; }
.t-row--grand { padding-top: 8px; margin-top: 2px; border-top: 1px solid #e5e7eb; }
.t-grand-lbl { font-size: 15px; font-weight: 800; color: #111827; }
.t-grand-val { font-size: 20px; font-weight: 800; color: #111827; font-variant-numeric: tabular-nums; }

/* Quick actions */
.quick-row {
  display: flex; gap: 6px;
  padding: 10px 16px 6px;
}
.q-action {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 4px;
  padding: 8px 4px; border-radius: 8px;
  border: 1.5px solid #e5e7eb; background: #f9fafb;
  color: #374151; font-size: 11px; font-weight: 600;
  cursor: pointer; transition: all .15s; font-family: var(--font-sans);
}
.q-action:hover:not(:disabled) { border-color: #9ca3af; background: #f3f4f6; }
.q-action--danger { color: #dc2626; border-color: #fecaca; }
.q-action--danger:hover:not(:disabled) { background: #fee2e2; }
.q-action--save { color: #1d4ed8; border-color: #bfdbfe; }
.q-action--save:hover:not(:disabled) { background: #eff6ff; }
.q-action:disabled { opacity: .4; cursor: not-allowed; }

/* Place order */
.place-btn {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  width: calc(100% - 32px); margin: 0 16px 14px;
  padding: 13px; border-radius: 10px;
  background: #16a34a; color: #fff;
  font-size: 14px; font-weight: 700; border: none; cursor: pointer;
  transition: background .15s, transform .1s;
  font-family: var(--font-sans);
  box-shadow: 0 4px 12px rgba(22,163,74,.28);
}
.place-btn:hover:not(:disabled) { background: #15803d; }
.place-btn:active:not(:disabled) { transform: scale(.98); }
.place-btn:disabled { background: #d1d5db; color: #9ca3af; cursor: not-allowed; box-shadow: none; }

.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,.3);
  border-top-color: #fff; border-radius: 50%;
  animation: spin .7s linear infinite; flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
