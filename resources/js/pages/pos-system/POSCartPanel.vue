<template>
  <div class="cart-root">
    <div class="cart-scroll scrollbar-thin">

      <!-- Customer section -->
      <div class="section-block">
        <div class="section-row-between">
          <div class="section-row-left">
            <UserIcon :size="14" />
            <span class="section-title">Customer</span>
          </div>
          <button class="link-btn" @click="togglePicker">Change</button>
        </div>

        <!-- Customer picker -->
        <div v-if="showPicker" class="picker-box">
          <div class="picker-search-row">
            <SearchIcon :size="13" style="color:#9ca3af;flex-shrink:0" />
            <input
              v-model="custSearch"
              placeholder="Search customer..."
              class="picker-inp"
              @input="onCustSearch"
            />
          </div>
          <div class="picker-list scrollbar-thin">
            <div v-if="custLoading" class="picker-loading">Searching...</div>
            <button
              v-for="c in customerResults"
              :key="c.id ?? 'walkin'"
              class="picker-row"
              :class="{ active: posStore.customerId === c.id }"
              @click="selectCustomer(c)"
            >
              <div class="p-avatar">{{ (c.name || 'W').charAt(0) }}</div>
              <div>
                <p class="p-name">{{ c.name }}</p>
                <p class="p-email">{{ c.email || c.mobile || 'Walk-in guest' }}</p>
              </div>
            </button>
          </div>
        </div>

        <!-- Selected customer card -->
        <div v-else class="cust-card">
          <div class="cust-avatar">{{ (posStore.customerObj?.name || 'W').charAt(0) }}</div>
          <div class="cust-info">
            <p class="cust-name">{{ posStore.customerObj?.name ?? 'Walk-In Guest' }}</p>
            <p v-if="posStore.customerObj?.email" class="cust-meta">{{ posStore.customerObj.email }}</p>
            <p v-if="posStore.customerObj?.mobile" class="cust-meta">{{ posStore.customerObj.mobile }}</p>
          </div>
        </div>
      </div>

      <!-- Create customer mini form -->
      <div v-if="showCreateCustomer" class="section-block">
        <p class="section-title" style="margin-bottom:8px">New Customer</p>
        <div class="field" style="margin-bottom:6px">
          <label class="field-lbl">NAME *</label>
          <input v-model="newCust.company_name" class="field-inp" placeholder="Customer name" />
        </div>
        <div class="two-col" style="margin-bottom:6px">
          <div class="field">
            <label class="field-lbl">MOBILE</label>
            <input v-model="newCust.mobile" class="field-inp" placeholder="Mobile" />
          </div>
          <div class="field">
            <label class="field-lbl">EMAIL</label>
            <input v-model="newCust.email" class="field-inp" placeholder="Email" type="email" />
          </div>
        </div>
        <div class="two-col">
          <button class="q-action" @click="createCustomer" :disabled="!newCust.company_name || creatingCust">
            {{ creatingCust ? 'Saving...' : 'Save' }}
          </button>
          <button class="q-action" @click="showCreateCustomer = false; newCust = {}">Cancel</button>
        </div>
      </div>

      <!-- Order type -->
      <div class="section-block section-block--noborder">
        <div class="bill-type-row">
          <button
            v-for="t in ['Dine In','Takeaway','Delivery']"
            :key="t"
            class="bill-btn"
            :class="{ active: posStore.billType === t }"
            @click="posStore.billType = t"
          >{{ t }}</button>
        </div>

        <!-- Dine In options -->
        <template v-if="posStore.billType === 'Dine In'">
          <div class="two-col" style="margin-top:8px">
            <div class="field">
              <label class="field-lbl">TABLE</label>
              <div class="sel-wrap">
                <Select filter v-model="posStore.tableId" class="field-sel" :options="posStore.tables" optionLabel="table_name" optionValue="id" placeholder="Select table">
                  <template #value="slotProps">
                    <span v-if="slotProps.value">{{ posStore.tables?.find(t => t.id === slotProps.value)?.table_name }}{{ posStore.tables?.find(t => t.id === slotProps.value)?.status === 'Occupied' ? ' (Occupied)' : '' }}</span>
                    <span v-else>{{ slotProps.placeholder }}</span>
                  </template>
                  <template #option="slotProps">
                    <span>{{ slotProps.option.table_name }}{{ slotProps.option.status === 'Occupied' ? ' (Occupied)' : '' }}</span>
                  </template>
                </Select>
                <ChevronDownIcon :size="12" class="sel-arr" />
              </div>
            </div>
            <div class="field">
              <label class="field-lbl">WAITER</label>
              <div class="sel-wrap">
                <Select filter v-model="posStore.waiterId" class="field-sel" :options="posStore.waiters" optionLabel="name" optionValue="id" placeholder="Select waiter">
                  <template #value="slotProps">
                    <span v-if="slotProps.value">{{ posStore.waiters.find(w => w.id === slotProps.value)?.name }}</span>
                    <span v-else>{{ slotProps.placeholder }}</span>
                  </template>
                  <template #option="slotProps">
                    <span>{{ slotProps.option.name }}</span>
                  </template>
                </Select>
                <ChevronDownIcon :size="12" class="sel-arr" />
              </div>
            </div>
          </div>
          <div class="field" style="margin-top:8px">
            <label class="field-lbl">COVERS / PAX</label>
            <input v-model.number="posStore.covers" type="number" min="1" class="field-inp" />
          </div>
        </template>

        <!-- Delivery options -->
        <template v-if="posStore.billType === 'Delivery'">
          <div class="field" style="margin-top:8px">
            <label class="field-lbl">DELIVERY ADDRESS</label>
            <textarea v-model="posStore.deliveryAddress" rows="2" class="field-inp" placeholder="Full delivery address" style="resize:none" />
          </div>
          <div class="two-col" style="margin-top:8px">
            <div class="field">
              <label class="field-lbl">DELIVERY CHARGE</label>
              <input v-model.number="posStore.deliveryCharge" type="number" min="0" class="field-inp" placeholder="0.00" />
            </div>
            <div class="field">
              <label class="field-lbl">DELIVERY NOTES</label>
              <input v-model="posStore.deliveryNotes" class="field-inp" placeholder="Notes" />
            </div>
          </div>
        </template>
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
        <div v-if="posStore.cartItems.length === 0" class="cart-empty">
          <div class="empty-icon"><PackageIcon :size="30" /></div>
          <p class="empty-t">Your cart is empty</p>
          <p class="empty-s">Add products to get started</p>
        </div>
        <div v-else class="cart-list">
          <div v-for="ci in posStore.cartItems" :key="ci.item_id" class="cart-row">
            <div class="row-thumb">
              <img :src="ci.image" :alt="ci.name" />
            </div>
            <div class="row-info">
              <p class="row-name">{{ ci.name }}</p>
              <p class="row-cat">{{ ci.category }}</p>
              <p class="row-unit">{{ formatCurrency(ci.price) }} × {{ ci.quantity }}</p>
            </div>
            <div class="row-qty">
              <button class="q-btn" @click="posStore.updateQty(ci.item_id, -1)"><MinusIcon :size="10" /></button>
              <span class="q-num">{{ ci.quantity }}</span>
              <button class="q-btn q-btn--add" @click="posStore.updateQty(ci.item_id, 1)"><PlusIcon :size="10" /></button>
            </div>
            <div class="row-right">
              <span class="row-total">{{ formatCurrency(ci.price * ci.quantity) }}</span>
              <button class="row-del" @click="posStore.removeItem(ci.item_id)" title="Remove"><XIcon :size="11" /></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Discount + Notes -->
      <div class="section-block">
        <div class="two-col">
          <div class="field">
            <label class="field-lbl">DISCOUNT</label>
            <input v-model.number="posStore.discount" type="number" min="0" placeholder="0.00" class="field-inp" />
          </div>
          <div class="field">
            <label class="field-lbl">TYPE</label>
            <div class="sel-wrap">
              <Select filter v-model="posStore.discountType" class="field-sel" :options="[{label: 'Flat ($)', value: 'flat'}, {label: 'Percent (%)', value: 'percent'}]" optionLabel="label" optionValue="value" />
              <ChevronDownIcon :size="12" class="sel-arr" />
            </div>
          </div>
        </div>
        <div class="field" style="margin-top:8px">
          <label class="field-lbl">NOTE</label>
          <input v-model="posStore.notes" type="text" placeholder="Special requests, allergies..." class="field-inp" />
        </div>
      </div>

      <!-- Payment -->
      <div class="section-block">
        <div class="field">
          <label class="field-lbl">PAYMENT NOTE</label>
          <textarea v-model="posStore.paymentNote" rows="2" placeholder="Payment note..." class="field-inp" style="resize:none" />
        </div>
      </div>

      <!-- Totals -->
      <div class="section-block totals">
        <div class="t-row"><span class="t-lbl">Total Items:</span><span class="t-val">{{ posStore.itemCount }}</span></div>
        <div class="t-row"><span class="t-lbl">Sub Total:</span><span class="t-val">{{ formatCurrency(posStore.subtotal) }}</span></div>
        <div class="t-row"><span class="t-lbl">Discount:</span><span class="t-val">{{ formatCurrency(posStore.discountAmt) }}</span></div>
        <div class="t-row"><span class="t-lbl">Tax:</span><span class="t-val">{{ formatCurrency(posStore.taxAmount) }}</span></div>
        <div class="t-row"><span class="t-lbl">Total:</span><span class="t-val">{{ formatCurrency(posStore.total) }}</span></div>
        <div class="t-row"><span class="t-lbl">Round Off:</span><span class="t-val">{{ formatCurrency(posStore.roundOff) }}</span></div>
        <div class="t-row t-row--grand">
          <span class="t-grand-lbl">Net Payable:</span>
          <span class="t-grand-val">{{ formatCurrency(posStore.netPayable) }}</span>
        </div>
        <div class="t-row"><span class="t-lbl">Balance:</span><span class="t-val">{{ formatCurrency(posStore.balance) }}</span></div>
      </div>

      <!-- Action buttons -->
      <div class="quick-row">
        <button v-if="can('pos.kot.create')" class="q-action" :disabled="!hasItems" @click="$emit('kot')">
          <FileTextIcon :size="13" /> KOT
        </button>
        <button class="q-action" :disabled="!hasItems" @click="$emit('bill-preview')">
          <PrinterIcon :size="13" /> Bill Preview
        </button>
        <button v-if="can('pos.orders.delete')" class="q-action q-action--danger" :disabled="!hasItems" @click="posStore.clearCart()">
          <XIcon :size="13" /> Cancel
        </button>
      </div>

      <button v-if="can('pos.orders.create')" class="place-btn" :disabled="!hasItems || isPlacing" @click="handlePlace">
        <span v-if="isPlacing" class="spinner" />
        <CheckCircleIcon v-else :size="16" />
        {{ isPlacing ? 'Processing…' : 'Place Order' }}
      </button>
      <button v-if="can('pos.orders.create')" class="place-btn pay-btn" style="margin-top:6px" :disabled="!hasItems || isPlacing" @click="handlePlaceStripe">
        Pay Now
      </button>

    </div>
    <div class="cart-bottom" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
  User as UserIcon, Search as SearchIcon, ShoppingCart as ShoppingCartIcon,
  Trash2 as Trash2Icon, Minus as MinusIcon, Plus as PlusIcon, X as XIcon,
  ChevronDown as ChevronDownIcon, Package as PackageIcon,
  FileText as FileTextIcon, Printer as PrinterIcon, CheckCircle as CheckCircleIcon,
} from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { posApi } from '@/services/settingsApi.js';
import { usePermission } from '@/composables/usePermission.js';

const { can } = usePermission();

const emit = defineEmits(['kot', 'bill-preview', 'place-order']);
const posStore = usePOSStore();

const showPicker        = ref(false);
const custSearch        = ref('');
const custLoading       = ref(false);
const customerResults   = ref([{ id: null, name: 'Walk-In Guest', email: null, mobile: null }]);
const isPlacing         = ref(false);
const showCreateCustomer = ref(false);
const creatingCust      = ref(false);
const newCust           = ref({});

const hasItems = computed(() => posStore.cartItems.length > 0);

function togglePicker() {
  showPicker.value = !showPicker.value;
  if (showPicker.value && customerResults.value.length <= 1) {
    onCustSearch();
  }
}

let custTimer = null;
function onCustSearch() {
  clearTimeout(custTimer);
  custTimer = setTimeout(async () => {
    custLoading.value = true;
    try {
      const res = await posApi.searchCustomers(custSearch.value);
      customerResults.value = res.data;
    } finally {
      custLoading.value = false;
    }
  }, 300);
}

function selectCustomer(c) {
  posStore.customerId  = c.id;
  posStore.customerObj = c;
  showPicker.value     = false;
  custSearch.value     = '';
}

async function createCustomer() {
  if (!newCust.value.company_name) return;
  creatingCust.value = true;
  try {
    const res = await posApi.createCustomer(newCust.value);
    selectCustomer(res.data);
    showCreateCustomer.value = false;
    newCust.value = {};
  } finally {
    creatingCust.value = false;
  }
}

function formatCurrency(val) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val ?? 0);
}

async function handlePlace() {
  if (!hasItems.value) return;
  isPlacing.value = true;
  try {
    await new Promise(r => setTimeout(r, 0)); // flush UI
    emit('place-order');
  } finally {
    isPlacing.value = false;
  }
}

async function handlePlaceStripe() {
  if (!hasItems.value) return;
  isPlacing.value = true;
  try {
    const res = await posApi.createStripeSession(posStore.netPayable, posStore.billType);
    if (res && res.checkout_url) {
      window.location.href = res.checkout_url;
    }
  } catch (e) {
    alert('Failed to initiate Stripe checkout');
  } finally {
    isPlacing.value = false;
  }
}
</script>

<style scoped>
.cart-root { display: flex; flex-direction: column; height: 100%; background: var(--card); }
.cart-scroll { flex: 1; overflow-y: auto; padding-bottom: 8px; }
.cart-bottom { flex-shrink: 0; }

.section-block {
  padding: 12px 16px;
  border-bottom: 1px solid var(--border);
}
.section-block--noborder { border-bottom: none; }
.section-row-between { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.section-row-left { display: flex; align-items: center; gap: 6px; }
.section-title { font-size: 13px; font-weight: 700; color: var(--foreground); }
.link-btn { font-size: 12px; font-weight: 600; color: var(--primary); background: none; border: none; cursor: pointer; font-family: var(--font-sans); }

.picker-box { background: var(--muted); border-radius: 10px; overflow: hidden; }
.picker-search-row { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-bottom: 1px solid var(--border); }
.picker-inp { flex: 1; background: transparent; border: none; outline: none; font-size: 12px; color: var(--foreground); font-family: var(--font-sans); }
.picker-list { max-height: 160px; overflow-y: auto; }
.picker-loading { font-size: 11px; color: var(--muted-foreground); padding: 10px 12px; }
.picker-row {
  width: 100%; display: flex; align-items: center; gap: 10px;
  padding: 8px 12px; background: none; border: none; cursor: pointer;
  font-family: var(--font-sans); transition: background .1s;
}
.picker-row:hover { background: var(--border); }
.picker-row.active { background: var(--accent); }
.p-avatar {
  width: 28px; height: 28px; border-radius: 50%;
  background: var(--primary); color: var(--primary-foreground);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.p-name { font-size: 12px; font-weight: 600; color: var(--foreground); text-align: left; margin: 0; }
.p-email { font-size: 10px; color: var(--muted-foreground); text-align: left; margin: 0; }
.create-cust-btn {
  width: 100%; display: flex; align-items: center; justify-content: center; gap: 4px;
  padding: 8px; font-size: 11px; font-weight: 700; color: var(--primary);
  background: none; border: none; border-top: 1px solid var(--border);
  cursor: pointer; font-family: var(--font-sans);
}

.cust-card { display: flex; align-items: center; gap: 10px; }
.cust-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--primary); color: var(--primary-foreground);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; flex-shrink: 0;
}
.cust-info { flex: 1; min-width: 0; }
.cust-name { font-size: 13px; font-weight: 700; color: var(--foreground); margin: 0; }
.cust-meta { font-size: 11px; color: var(--muted-foreground); margin: 2px 0 0; }

.bill-type-row { display: flex; gap: 6px; }
.bill-btn {
  flex: 1; padding: 7px 4px; border-radius: 8px;
  font-size: 12px; font-weight: 600;
  border: 1.5px solid var(--border); background: var(--muted); color: var(--muted-foreground);
  cursor: pointer; transition: all .15s; font-family: var(--font-sans);
}
.bill-btn:hover { border-color: var(--primary); color: var(--primary); }
.bill-btn.active { background: var(--primary); color: var(--primary-foreground); border-color: var(--primary); }

.cart-head-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 16px 6px;
}
.clear-btn {
  display: flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700; color: var(--danger);
  background: none; border: none; cursor: pointer; font-family: var(--font-sans);
}

.cart-items-area { padding: 0 12px; }
.cart-empty {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; min-height: 120px; gap: 6px; text-align: center;
}
.empty-icon {
  width: 48px; height: 48px; border-radius: 12px;
  background: var(--muted); color: var(--muted-foreground);
  display: flex; align-items: center; justify-content: center;
}
.empty-t { font-size: 13px; font-weight: 700; color: var(--foreground); margin: 0; }
.empty-s { font-size: 11px; color: var(--muted-foreground); margin: 0; }

.cart-list { display: flex; flex-direction: column; gap: 6px; padding: 4px 0 8px; }
.cart-row {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px; border-radius: 10px;
  background: var(--muted); border: 1px solid var(--border);
}
.cart-row:hover { border-color: var(--primary); }
.row-thumb { width: 38px; height: 38px; border-radius: 8px; overflow: hidden; flex-shrink: 0; }
.row-thumb img { width: 100%; height: 100%; object-fit: cover; }
.row-info { flex: 1; min-width: 0; }
.row-name { font-size: 11px; font-weight: 700; color: var(--foreground); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.row-cat  { font-size: 10px; color: var(--muted-foreground); margin: 1px 0 0; }
.row-unit { font-size: 10px; color: var(--muted-foreground); margin: 1px 0 0; }
.row-qty { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
.q-btn {
  width: 22px; height: 22px; border-radius: 6px;
  border: 1px solid var(--border); background: var(--background);
  color: var(--foreground); cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.q-btn--add { background: var(--primary); color: var(--primary-foreground); border-color: var(--primary); }
.q-num { width: 22px; text-align: center; font-size: 13px; font-weight: 700; color: var(--foreground); }
.row-right { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; flex-shrink: 0; }
.row-total { font-size: 12px; font-weight: 700; color: var(--foreground); font-variant-numeric: tabular-nums; }
.row-del {
  width: 18px; height: 18px; border-radius: 4px;
  border: none; background: none; color: var(--muted-foreground);
  cursor: pointer; display: flex; align-items: center; justify-content: center;
}
.row-del:hover { background: var(--danger); color: #fff; border-radius: 4px; }

.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.field { display: flex; flex-direction: column; gap: 4px; }
.field-lbl {
  font-size: 10px; font-weight: 700; color: var(--muted-foreground);
  text-transform: uppercase; letter-spacing: .05em;
}
.sel-wrap { position: relative; }
.field-sel {
  width: 100%; padding: 8px 28px 8px 10px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--input);
  color: var(--foreground); font-size: 12px; font-family: var(--font-sans);
  appearance: none; outline: none; cursor: pointer;
}
.field-sel:focus { border-color: var(--primary); }
.sel-arr { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: var(--muted-foreground); pointer-events: none; }
.field-inp {
  width: 100%; padding: 8px 10px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--input);
  color: var(--foreground); font-size: 12px; font-family: var(--font-sans); outline: none;
  box-sizing: border-box;
}
.field-inp:focus { border-color: var(--primary); }

.totals { display: flex; flex-direction: column; gap: 6px; }
.t-row { display: flex; justify-content: space-between; align-items: center; }
.t-lbl { font-size: 12px; color: var(--muted-foreground); }
.t-val { font-size: 13px; font-weight: 600; color: var(--foreground); font-variant-numeric: tabular-nums; }
.t-row--grand { padding-top: 8px; border-top: 1px dashed var(--border); margin-top: 4px; }
.t-grand-lbl { font-size: 14px; font-weight: 700; color: var(--foreground); }
.t-grand-val { font-size: 18px; font-weight: 800; color: var(--primary); font-variant-numeric: tabular-nums; }

.quick-row {
  display: flex; align-items: center; gap: 6px;
  padding: 10px 16px;
}
.q-action {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 4px;
  padding: 8px 4px; border-radius: 9px;
  border: 1.5px solid var(--border); background: var(--muted); color: var(--foreground);
  font-size: 11px; font-weight: 700; cursor: pointer; transition: all .15s;
  font-family: var(--font-sans);
}
.q-action:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); background: var(--accent); }
.q-action:disabled { opacity: .4; cursor: not-allowed; }
.q-action--danger { color: var(--danger); border-color: color-mix(in srgb, var(--danger) 30%, transparent); }
.q-action--danger:hover:not(:disabled) { background: color-mix(in srgb, var(--danger) 10%, transparent); border-color: var(--danger); }

.place-btn {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  width: calc(100% - 32px); margin: 0 16px;
  padding: 13px; border-radius: 12px;
  background: linear-gradient(135deg, #E85D26 0%, #F07040 100%);
  color: #fff; font-size: 14px; font-weight: 700;
  border: none; cursor: pointer; transition: opacity .15s, transform .1s;
  font-family: var(--font-sans); box-shadow: 0 4px 14px rgba(232,93,38,.35);
  box-sizing: border-box;
}
.place-btn:hover:not(:disabled) { opacity: .9; }
.place-btn:active:not(:disabled) { transform: scale(.98); }
.place-btn:disabled { opacity: .4; cursor: not-allowed; box-shadow: none; }
.pay-btn { background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); box-shadow: 0 4px 14px rgba(22,163,74,.3); margin-bottom: 16px; }

.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,.3); border-top-color: #fff;
  border-radius: 50%; animation: spin .7s linear infinite; flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
