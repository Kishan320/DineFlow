<template>
  <div class="pos-root">

    <!-- New Order / On Going Orders tabs -->
    <div class="pos-order-tabs">
      <button
        v-for="m in [{id:'new',label:'New Order'},{id:'ongoing',label:'On Going Orders'}]"
        :key="m.id"
        @click="posStore.orderMode = m.id"
        class="order-tab"
        :class="{ active: posStore.orderMode === m.id }"
      >
        {{ m.label }}
        <span v-if="m.id === 'ongoing' && posStore.activeOngoingCount > 0" class="order-tab-badge">
          {{ posStore.activeOngoingCount }}
        </span>
      </button>
    </div>

    <!-- ONGOING VIEW -->
    <div v-if="posStore.orderMode === 'ongoing'" class="pos-ongoing-view">
      <OngoingOrdersPanel
        :orders="posStore.ongoingOrders"
        :loading="posStore.loadingOngoing"
        @resume="posStore.orderMode = 'new'"
        @kot="openKotForOrder"
        @bill="openBillForOrder"
        @complete="handleComplete"
        @cancel="handleCancel"
      />
    </div>

    <!-- NEW ORDER VIEW -->
    <template v-else>
      <div v-if="!isDesktop" class="pos-tabs">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          class="pos-tab"
          :class="{ active: activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          <component :is="tab.icon" :size="14" />
          <span>{{ tab.label }}</span>
          <span v-if="tab.id === 'cart' && posStore.itemCount > 0" class="tab-badge">
            {{ posStore.itemCount }}
          </span>
        </button>
      </div>

      <div class="pos-body">
        <div v-show="isDesktop || activeTab === 'products'" class="pos-products">
          <POSProducts
            :products="posStore.products"
            :categories="posStore.categories"
            :meta="posStore.productsMeta"
            :loading="posStore.loadingProducts"
            :session="posStore.session"
            @add="posStore.addToCart"
            @load-products="posStore.loadProducts"
          />
        </div>
        <div
          v-show="isDesktop || activeTab === 'cart'"
          class="pos-cart-panel"
          :class="{ 'pos-cart-panel--mobile': !isDesktop }"
        >
          <POSCartPanel
            @kot="openKot"
            @bill-preview="openBillPreview"
            @place-order="handlePlaceOrder"
          />
        </div>
      </div>
    </template>

    <!-- KOT Modal -->
    <KOTModal
      v-if="kotOpen"
      :order="kotOrder"
      @close="kotOpen = false; kotOrder = null"
    />

    <!-- Bill Preview Modal -->
    <BillPreviewModal
      v-if="billOpen"
      :order="billOrder"
      @close="billOpen = false; billOrder = null"
    />

    <!-- Order Success Modal -->
    <OrderSuccessModal
      v-if="successOpen"
      :order="lastOrder"
      @close="successOpen = false; lastOrder = null"
      @new-order="successOpen = false; lastOrder = null; activeTab = 'products'"
    />

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { LayoutGrid, ShoppingCart } from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import POSProducts       from './POSProducts.vue';
import POSCartPanel      from './POSCartPanel.vue';
import KOTModal          from './KOTModal.vue';
import BillPreviewModal  from './BillPreviewModal.vue';
import OrderSuccessModal from './OrderSuccessModal.vue';
import OngoingOrdersPanel from './OngoingOrdersPanel.vue';

const posStore    = usePOSStore();
const kotOpen     = ref(false);
const billOpen    = ref(false);
const successOpen = ref(false);
const lastOrder   = ref(null);
const activeTab   = ref('products');
const kotOrder    = ref(null);
const billOrder   = ref(null);

const isDesktop = ref(window.innerWidth >= 1024);
function onResize() { isDesktop.value = window.innerWidth >= 1024; }
onMounted(async () => {
  window.addEventListener('resize', onResize);
  await Promise.all([
    posStore.loadActiveSession(),
    posStore.loadProducts(),
    posStore.loadCategories(),
    posStore.loadTables(),
    posStore.loadWaiters(),
    posStore.loadOngoing(),
  ]);
});
onUnmounted(() => window.removeEventListener('resize', onResize));

const tabs = [
  { id: 'products', label: 'Products', icon: LayoutGrid },
  { id: 'cart',     label: 'Cart',     icon: ShoppingCart },
];

function openKot() {
  kotOrder.value = null;
  kotOpen.value  = true;
}

function openKotForOrder(order) {
  kotOrder.value = order;
  kotOpen.value  = true;
}

function openBillPreview() {
  billOrder.value = null;
  billOpen.value  = true;
}

function openBillForOrder(order) {
  billOrder.value = order;
  billOpen.value  = true;
}

async function handleComplete(orderId) {
  await posStore.completeOrder(orderId);
}

async function handleCancel(orderId) {
  await posStore.cancelOrder(orderId);
}

async function handlePlaceOrder() {
  try {
    const order = await posStore.placeOrder();
    lastOrder.value   = order;
    successOpen.value = true;
    activeTab.value   = 'products';
  } catch (err) {
    console.error('Order failed:', err);
  }
}
</script>
