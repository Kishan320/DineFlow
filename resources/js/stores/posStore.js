import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import { posApi } from '@/services/settingsApi.js';

const LS_KEY = 'pos_cart_state';

function loadCart() {
  try {
    return JSON.parse(localStorage.getItem(LS_KEY) || 'null');
  } catch {
    return null;
  }
}

function saveCart(state) {
  try {
    localStorage.setItem(LS_KEY, JSON.stringify(state));
  } catch { /* quota exceeded — ignore */ }
}

export const usePOSStore = defineStore('pos', () => {

  const _saved = loadCart();

  // ── Session ──────────────────────────────────────────────────
  const session     = ref(null);
  const sessionCode = computed(() => session.value?.session_code ?? null);

  // ── Cart state ───────────────────────────────────────────────
  const cartItems   = ref(_saved?.cartItems   ?? []);
  const customerId  = ref(_saved?.customerId  ?? null);
  const customerObj = ref(_saved?.customerObj ?? null);
  const tableId     = ref(_saved?.tableId     ?? null);
  const waiterId    = ref(_saved?.waiterId    ?? null);
  const billType    = ref(_saved?.billType    ?? 'Dine In');
  const covers      = ref(_saved?.covers      ?? 1);
  const discount    = ref(_saved?.discount    ?? 0);
  const discountType = ref(_saved?.discountType ?? 'flat');
  const notes       = ref(_saved?.notes       ?? '');
  const orderMode   = ref('new');
  const currentOrderId = ref(_saved?.currentOrderId ?? null);

  // Payment
  const billPayType  = ref(_saved?.billPayType  ?? 'Cash');
  const cashAmt      = ref(_saved?.cashAmt      ?? 0);
  const cardRef      = ref(_saved?.cardRef      ?? '');
  const cardAmt      = ref(_saved?.cardAmt      ?? 0);
  const othersType   = ref(_saved?.othersType   ?? '');
  const othersRef    = ref(_saved?.othersRef    ?? '');
  const othersAmt    = ref(_saved?.othersAmt    ?? 0);
  const upiAmt       = ref(_saved?.upiAmt       ?? 0);
  const upiRef       = ref(_saved?.upiRef       ?? '');
  const paymentNote  = ref(_saved?.paymentNote  ?? '');

  // Delivery
  const deliveryAddress = ref(_saved?.deliveryAddress ?? '');
  const deliveryCharge  = ref(_saved?.deliveryCharge  ?? 0);
  const deliveryNotes   = ref(_saved?.deliveryNotes   ?? '');

  // ── Ongoing orders (from DB, refreshed on load) ──────────────
  const ongoingOrders = ref([]);
  const loadingOngoing = ref(false);

  // ── Catalog data ─────────────────────────────────────────────
  const products    = ref([]);
  const categories  = ref([]);
  const tables      = ref([]);
  const waiters     = ref([]);
  const loadingProducts  = ref(false);
  const productsMeta     = ref({ current_page: 1, last_page: 1, total: 0, per_page: 24 });

  // ── Computed totals ──────────────────────────────────────────
  const subtotal = computed(() =>
    cartItems.value.reduce((s, ci) => s + ci.price * ci.quantity, 0)
  );

  const taxAmount = computed(() => {
    let tax = 0;
    for (const ci of cartItems.value) {
      const percent = ci.tax_percent ?? 0;
      if (percent > 0) {
        if (ci.tax_type === 'Inclusive') {
          tax += ci.price * ci.quantity - (ci.price * ci.quantity) / (1 + percent / 100);
        } else {
          tax += ci.price * ci.quantity * percent / 100;
        }
      }
    }
    return Math.round(tax * 100) / 100;
  });

  const discountAmt = computed(() =>
    discountType.value === 'percent'
      ? Math.round(subtotal.value * discount.value / 100 * 100) / 100
      : Math.min(discount.value, subtotal.value)
  );

  const total      = computed(() => Math.round((subtotal.value + taxAmount.value - discountAmt.value) * 100) / 100);
  const roundOff   = computed(() => Math.round((Math.round(total.value) - total.value) * 100) / 100);
  const netPayable = computed(() => Math.round(total.value));
  const amountPaid = computed(() => (cashAmt.value || 0) + (cardAmt.value || 0) + (othersAmt.value || 0) + (upiAmt.value || 0));
  const balance    = computed(() => Math.round((netPayable.value - amountPaid.value) * 100) / 100);
  const itemCount  = computed(() => cartItems.value.reduce((s, ci) => s + ci.quantity, 0));

  const activeOngoingCount = computed(() =>
    ongoingOrders.value.filter(o => !['Completed', 'Cancelled'].includes(o.status)).length
  );

  // ── Session actions ──────────────────────────────────────────
  async function loadActiveSession() {
    try {
      const res = await posApi.getActiveSession();
      session.value = res.data ?? null;
    } catch {
      session.value = null;
    }
  }

  async function openSession(data = {}) {
    const res = await posApi.openSession(data);
    session.value = res.data;
    return res.data;
  }

  async function closeSession(closingBalance = 0) {
    if (!session.value) return;
    const res = await posApi.closeSession(session.value.id, { closing_balance: closingBalance });
    session.value = null;
    return res.data;
  }

  async function destroySession() {
    if (!session.value) return;
    await posApi.destroySession(session.value.id);
    session.value = null;
    clearCart();
  }

  // ── Catalog loaders ──────────────────────────────────────────
  async function loadProducts(params = {}) {
    loadingProducts.value = true;
    try {
      const res = await posApi.getProducts({ per_page: 24, ...params });
      products.value     = res.data;
      productsMeta.value = {
        current_page: res.current_page,
        last_page:    res.last_page,
        total:        res.total,
        per_page:     res.per_page,
      };
    } finally {
      loadingProducts.value = false;
    }
  }

  async function loadCategories() {
    const res = await posApi.getCategories();
    categories.value = [{ id: null, label: 'All Categories' }, ...res.data];
  }

  async function loadTables() {
    const res = await posApi.getTables();
    tables.value = res.data;
  }

  async function loadWaiters() {
    const res = await posApi.getWaiters();
    waiters.value = res.data;
  }

  async function loadOngoing() {
    loadingOngoing.value = true;
    try {
      const res = await posApi.getOngoing();
      ongoingOrders.value = res.data;
    } finally {
      loadingOngoing.value = false;
    }
  }

  // ── Persist cart to localStorage on every change ─────────────────
  watch(
    () => ({
      cartItems:       cartItems.value,
      customerId:      customerId.value,
      customerObj:     customerObj.value,
      tableId:         tableId.value,
      waiterId:        waiterId.value,
      billType:        billType.value,
      covers:          covers.value,
      discount:        discount.value,
      discountType:    discountType.value,
      notes:           notes.value,
      currentOrderId:  currentOrderId.value,
      billPayType:     billPayType.value,
      cashAmt:         cashAmt.value,
      cardRef:         cardRef.value,
      cardAmt:         cardAmt.value,
      othersType:      othersType.value,
      othersRef:       othersRef.value,
      othersAmt:       othersAmt.value,
      upiAmt:          upiAmt.value,
      upiRef:          upiRef.value,
      paymentNote:     paymentNote.value,
      deliveryAddress: deliveryAddress.value,
      deliveryCharge:  deliveryCharge.value,
      deliveryNotes:   deliveryNotes.value,
    }),
    (val) => saveCart(val),
    { deep: true }
  );

  // ── Cart actions ─────────────────────────────────────────────
  function addToCart(product) {
    const existing = cartItems.value.find(ci => ci.item_id === product.id);
    if (existing) {
      existing.quantity += 1;
    } else {
      cartItems.value.push({
        item_id:     product.id,
        name:        product.name,
        code:        product.code,
        category:    product.category,
        price:       product.price,
        tax_percent: product.tax_percent ?? 0,
        tax_type:    product.tax_type ?? 'Exclusive',
        image:       product.image,
        quantity:    1,
        kot_printed: false,
      });
    }
  }

  function updateQty(itemId, delta) {
    const idx = cartItems.value.findIndex(ci => ci.item_id === itemId);
    if (idx === -1) return;
    const newQty = cartItems.value[idx].quantity + delta;
    if (newQty <= 0) {
      cartItems.value.splice(idx, 1);
    } else {
      cartItems.value[idx].quantity = newQty;
    }
  }

  function removeItem(itemId) {
    cartItems.value = cartItems.value.filter(ci => ci.item_id !== itemId);
  }

  function clearCart() {
    cartItems.value     = [];
    customerId.value    = null;
    customerObj.value   = null;
    tableId.value       = null;
    waiterId.value      = null;
    billType.value      = 'Dine In';
    covers.value        = 1;
    discount.value      = 0;
    discountType.value  = 'flat';
    notes.value         = '';
    billPayType.value   = 'Cash';
    cashAmt.value       = 0;
    cardRef.value       = '';
    cardAmt.value       = 0;
    othersType.value    = '';
    othersRef.value     = '';
    othersAmt.value     = 0;
    upiAmt.value        = 0;
    upiRef.value        = '';
    paymentNote.value   = '';
    deliveryAddress.value = '';
    deliveryCharge.value  = 0;
    deliveryNotes.value   = '';
    currentOrderId.value  = null;
    localStorage.removeItem(LS_KEY);
  }

  // ── Order submission ─────────────────────────────────────────
  async function placeOrder() {
    if (cartItems.value.length === 0) throw new Error('Cart is empty');

    const selectedTable  = tables.value.find(t => t.id === tableId.value);
    const selectedWaiter = waiters.value.find(w => w.id === waiterId.value);

    const payload = {
      cart_items: cartItems.value.map(ci => ({
        item_id:  ci.item_id,
        quantity: ci.quantity,
        notes:    ci.notes ?? null,
      })),
      session_id:       session.value?.id ?? null,
      customer_id:      customerId.value,
      customer_name:    customerObj.value?.name ?? 'Walk-In Guest',
      customer_phone:   customerObj.value?.mobile ?? null,
      table_id:         tableId.value,
      table_label:      selectedTable?.table_name ?? null,
      waiter_id:        waiterId.value,
      waiter_name:      selectedWaiter?.name ?? null,
      order_type:       billType.value,
      covers:           covers.value,
      notes:            notes.value,
      discount:         discount.value,
      discount_type:    discountType.value,
      bill_pay_type:    billPayType.value,
      cash_amt:         cashAmt.value,
      card_ref:         cardRef.value,
      card_amt:         cardAmt.value,
      others_type:      othersType.value,
      others_ref:       othersRef.value,
      others_amt:       othersAmt.value,
      upi_amt:          upiAmt.value,
      upi_ref:          upiRef.value,
      payment_note:     paymentNote.value,
      delivery_address: deliveryAddress.value,
      delivery_charge:  deliveryCharge.value,
      delivery_notes:   deliveryNotes.value,
    };

    const res = await posApi.createOrder(payload);
    const order = res.data;

    clearCart();
    await loadOngoing();
    return order;
  }

  async function updateOrderStatus(orderId, status) {
    await posApi.updateOrderStatus(orderId, status);
    await loadOngoing();
  }

  async function completeOrder(orderId) {
    await posApi.updateOrderStatus(orderId, 'Completed');
    await loadOngoing();
    if (currentOrderId.value === orderId) clearCart();
  }

  async function cancelOrder(orderId) {
    await posApi.updateOrderStatus(orderId, 'Cancelled');
    await loadOngoing();
    if (currentOrderId.value === orderId) clearCart();
  }

  function resumeOrder(order) {
    clearCart();
    currentOrderId.value = order.id;

    cartItems.value = (order.items ?? []).map(item => ({
      item_id:     item.item_id,
      name:        item.item_name,
      code:        item.item_code,
      category:    item.category,
      price:       parseFloat(item.unit_price),
      tax_percent: parseFloat(item.tax_percent),
      tax_type:    item.tax_type,
      image:       item.image ?? '/images/menu/placeholder.jpg',
      quantity:    item.quantity,
      kot_printed: item.kot_printed,
    }));

    customerId.value   = order.customer_id;
    tableId.value      = order.table_id;
    waiterId.value     = order.waiter_id;
    billType.value     = order.order_type;
    covers.value       = order.covers ?? 1;
    discount.value     = parseFloat(order.discount ?? 0);
    discountType.value = order.discount_type ?? 'flat';
    notes.value        = order.notes ?? '';
    billPayType.value  = order.bill_pay_type ?? 'Cash';
    cashAmt.value      = parseFloat(order.cash_amt ?? 0);
    cardRef.value      = order.card_ref ?? '';
    cardAmt.value      = parseFloat(order.card_amt ?? 0);
    othersType.value   = order.others_type ?? '';
    othersRef.value    = order.others_ref ?? '';
    othersAmt.value    = parseFloat(order.others_amt ?? 0);
    upiAmt.value       = parseFloat(order.upi_amt ?? 0);
    upiRef.value       = order.upi_ref ?? '';
    paymentNote.value  = order.payment_note ?? '';
    orderMode.value    = 'new';
  }

  // ── KOT ─────────────────────────────────────────────────────
  async function generateKot(orderId, kotItems, kotNotes = null) {
    const res = await posApi.createKot(orderId, { kot_items: kotItems, notes: kotNotes });
    await loadOngoing();
    return res.data;
  }

  return {
    // State
    session, sessionCode,
    cartItems, customerId, customerObj,
    tableId, waiterId, billType, covers,
    discount, discountType, notes, orderMode, currentOrderId,
    billPayType, cashAmt, cardRef, cardAmt,
    othersType, othersRef, othersAmt, upiAmt, upiRef, paymentNote,
    deliveryAddress, deliveryCharge, deliveryNotes,

    // Catalog
    products, categories, tables, waiters,
    loadingProducts, productsMeta, loadingOngoing,

    // Ongoing
    ongoingOrders, activeOngoingCount,

    // Computed
    subtotal, taxAmount, discountAmt, total, roundOff, netPayable, amountPaid, balance, itemCount,

    // Session actions
    loadActiveSession, openSession, closeSession, destroySession,

    // Catalog loaders
    loadProducts, loadCategories, loadTables, loadWaiters, loadOngoing,

    // Cart actions
    addToCart, updateQty, removeItem, clearCart,

    // Order actions
    placeOrder, updateOrderStatus, completeOrder, cancelOrder, resumeOrder,

    // KOT
    generateKot,
  };
});
