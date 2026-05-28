import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import { customers, tables, waiters } from '@/utils/mockData.js';

const LS_KEY = 'pos_ongoing_orders';

function genOrderNo() {
  const d = new Date();
  const yy = String(d.getFullYear()).slice(-2);
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');
  const seq = String(Math.floor(Math.random() * 900) + 100);
  return `${yy}${mm}${dd}-${seq}`;
}

function genKotNo(order) {
  return String((order.kots?.length ?? 0) + 1).padStart(4, '0');
}

function loadFromLS() {
  try {
    return JSON.parse(localStorage.getItem(LS_KEY) || '[]');
  } catch {
    return [];
  }
}

export const usePOSStore = defineStore('pos', () => {
  // ── Cart state ──────────────────────────────────────────────
  const cartItems   = ref([]);
  const customerId  = ref('cust-006');
  const tableId     = ref('');
  const waiterId    = ref('');
  const billType    = ref('Dine In');
  const covers      = ref(1);
  const discount    = ref(0);
  const notes       = ref('');
  const orderMode   = ref('new');

  // current ongoing order being edited (null = new order)
  const currentOrderId = ref(null);

  // Split payment
  const cashAmt     = ref(0);
  const cardRef     = ref('');
  const cardAmt     = ref(0);
  const othersType  = ref('');
  const othersRef   = ref('');
  const othersAmt   = ref(0);
  const paymentNote = ref('');
  const billPayType = ref('Cash');

  // ── Ongoing orders list (persisted in localStorage) ─────────
  const ongoingOrders = ref(loadFromLS());

  watch(ongoingOrders, (val) => {
    localStorage.setItem(LS_KEY, JSON.stringify(val));
  }, { deep: true });

  // ── Computed ─────────────────────────────────────────────────
  const subtotal  = computed(() => cartItems.value.reduce((s, ci) => s + ci.menuItem.price * ci.quantity, 0));
  const taxAmount = computed(() => subtotal.value * 0.08);
  const total     = computed(() => subtotal.value + taxAmount.value - discount.value);
  const roundOff  = computed(() => parseFloat((Math.round(total.value) - total.value).toFixed(2)));
  const netPayable = computed(() => Math.round(total.value));
  const amountPaid = computed(() => (cashAmt.value || 0) + (cardAmt.value || 0) + (othersAmt.value || 0));
  const balance   = computed(() => parseFloat((netPayable.value - amountPaid.value).toFixed(2)));
  const itemCount = computed(() => cartItems.value.reduce((s, ci) => s + ci.quantity, 0));

  const selectedCustomer = computed(() => customers.find(c => c.id === customerId.value));
  const selectedTable    = computed(() => tables.find(t => t.id === tableId.value));
  const selectedWaiter   = computed(() => waiters.find(w => w.id === waiterId.value));

  // ── Cart actions ─────────────────────────────────────────────
  function addToCart(item) {
    const existing = cartItems.value.find(ci => ci.menuItem.id === item.id);
    if (existing) { existing.quantity += 1; }
    else { cartItems.value.push({ menuItem: item, quantity: 1 }); }
  }

  function updateQty(itemId, delta) {
    const idx = cartItems.value.findIndex(ci => ci.menuItem.id === itemId);
    if (idx === -1) return;
    cartItems.value[idx].quantity = Math.max(0, cartItems.value[idx].quantity + delta);
    if (cartItems.value[idx].quantity === 0) cartItems.value.splice(idx, 1);
  }

  function removeItem(itemId) {
    cartItems.value = cartItems.value.filter(ci => ci.menuItem.id !== itemId);
  }

  function clearCart() {
    cartItems.value  = [];
    customerId.value = 'cust-006';
    tableId.value    = '';
    waiterId.value   = '';
    billType.value   = 'Dine In';
    covers.value     = 1;
    discount.value   = 0;
    notes.value      = '';
    billPayType.value = 'Cash';
    cashAmt.value    = 0;
    cardRef.value    = '';
    cardAmt.value    = 0;
    othersType.value = '';
    othersRef.value  = '';
    othersAmt.value  = 0;
    paymentNote.value = '';
    currentOrderId.value = null;
  }

  // ── Ongoing order actions ─────────────────────────────────────

  /** Save current cart as an ongoing order (create or update) */
  function saveOngoing() {
    if (cartItems.value.length === 0) return null;

    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const timeStr = `${pad(now.getDate())}/${pad(now.getMonth() + 1)}/${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}`;

    const snapshot = {
      id:           currentOrderId.value ?? genOrderNo(),
      customerId:   customerId.value,
      customerName: selectedCustomer.value?.name ?? 'Walk-In Guest',
      customerPhone: selectedCustomer.value?.phone ?? '',
      tableId:      tableId.value,
      tableLabel:   selectedTable.value ? `T${selectedTable.value.number}` : '—',
      waiterId:     waiterId.value,
      waiterName:   selectedWaiter.value?.name ?? '',
      orderType:    billType.value,
      covers:       covers.value,
      cartItems:    JSON.parse(JSON.stringify(cartItems.value)),
      discount:     discount.value,
      notes:        notes.value,
      billPayType:  billPayType.value,
      cashAmt:      cashAmt.value,
      cardRef:      cardRef.value,
      cardAmt:      cardAmt.value,
      othersType:   othersType.value,
      othersRef:    othersRef.value,
      othersAmt:    othersAmt.value,
      paymentNote:  paymentNote.value,
      subtotal:     subtotal.value,
      taxAmount:    taxAmount.value,
      total:        total.value,
      roundOff:     roundOff.value,
      netPayable:   netPayable.value,
      itemCount:    itemCount.value,
      status:       'Pending',
      createdAt:    currentOrderId.value
        ? (ongoingOrders.value.find(o => o.id === currentOrderId.value)?.createdAt ?? timeStr)
        : timeStr,
      updatedAt:    timeStr,
      kots:         currentOrderId.value
        ? (ongoingOrders.value.find(o => o.id === currentOrderId.value)?.kots ?? [])
        : [],
    };

    const idx = ongoingOrders.value.findIndex(o => o.id === snapshot.id);
    if (idx !== -1) {
      ongoingOrders.value[idx] = snapshot;
    } else {
      ongoingOrders.value.unshift(snapshot);
    }

    currentOrderId.value = snapshot.id;
    return snapshot.id;
  }

  /** Load an ongoing order back into the cart */
  function resumeOrder(orderId) {
    const order = ongoingOrders.value.find(o => o.id === orderId);
    if (!order) return;

    cartItems.value   = JSON.parse(JSON.stringify(order.cartItems));
    customerId.value  = order.customerId;
    tableId.value     = order.tableId;
    waiterId.value    = order.waiterId;
    billType.value    = order.orderType;
    covers.value      = order.covers;
    discount.value    = order.discount;
    notes.value       = order.notes;
    billPayType.value = order.billPayType;
    cashAmt.value     = order.cashAmt;
    cardRef.value     = order.cardRef;
    cardAmt.value     = order.cardAmt;
    othersType.value  = order.othersType;
    othersRef.value   = order.othersRef;
    othersAmt.value   = order.othersAmt;
    paymentNote.value = order.paymentNote;
    currentOrderId.value = order.id;
    orderMode.value   = 'new';
  }

  /** Mark order as completed and remove from ongoing list */
  function completeOrder(orderId) {
    const id = orderId ?? currentOrderId.value;
    ongoingOrders.value = ongoingOrders.value.filter(o => o.id !== id);
    if (currentOrderId.value === id) clearCart();
  }

  /** Cancel an ongoing order */
  function cancelOrder(orderId) {
    const idx = ongoingOrders.value.findIndex(o => o.id === orderId);
    if (idx !== -1) ongoingOrders.value[idx].status = 'Cancelled';
    // Remove cancelled after brief display — caller can decide
  }

  /** Update status of an ongoing order */
  function updateOrderStatus(orderId, status) {
    const order = ongoingOrders.value.find(o => o.id === orderId);
    if (order) order.status = status;
  }

  /** Add a KOT record to an ongoing order */
  function addKot(orderId, kotItems) {
    const order = ongoingOrders.value.find(o => o.id === orderId);
    if (!order) return null;
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const kot = {
      kotNo:     genKotNo(order),
      timestamp: `${pad(now.getDate())}-${pad(now.getMonth()+1)}-${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`,
      items:     kotItems,
    };
    order.kots.push(kot);
    // mark items as printed
    order.cartItems.forEach(ci => { ci.kotPrinted = true; });
    return kot;
  }

  return {
    cartItems, customerId, tableId, waiterId, billType, covers, discount, notes,
    orderMode, currentOrderId, billPayType, cashAmt, cardRef, cardAmt,
    othersType, othersRef, othersAmt, paymentNote,
    subtotal, taxAmount, total, roundOff, netPayable, amountPaid, balance, itemCount,
    selectedCustomer, selectedTable, selectedWaiter,
    ongoingOrders,
    addToCart, updateQty, removeItem, clearCart,
    saveOngoing, resumeOrder, completeOrder, cancelOrder, updateOrderStatus, addKot,
  };
});
