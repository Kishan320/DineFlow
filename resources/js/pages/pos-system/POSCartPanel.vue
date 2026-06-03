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
    <!-- ══ END SCROLLABLE ══ -->

    <!-- ══ FIXED BOTTOM ══ -->
    <div class="cart-bottom">

      <!-- Quick actions -->
      
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
