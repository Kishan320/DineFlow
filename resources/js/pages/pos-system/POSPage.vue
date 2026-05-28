<template>
  <div class="pos-root">

    <!-- New Order / On Going Orders tabs -->
    <div style="display:flex;gap:8px;padding:10px 16px 0">
      <button
        v-for="m in [{id:'new',label:'New Order'},{id:'ongoing',label:'On Going Orders'}]"
        :key="m.id"
        @click="posStore.orderMode = m.id"
        :style="posStore.orderMode === m.id ? 'font-weight:700;border-bottom:2px solid currentColor' : 'color:#6b7280'"
        style="background:none;border:none;cursor:pointer;padding:6px 12px;font-size:13px;font-family:var(--font-sans)"
      >
        {{ m.label }}
        <span
          v-if="m.id === 'ongoing' && activeOngoingCount > 0"
          style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:#E85D26;color:#fff;font-size:10px;font-weight:700;margin-left:4px"
        >{{ activeOngoingCount }}</span>
      </button>
    </div>

    <!-- ── ONGOING ORDERS VIEW ── -->
    <div v-if="posStore.orderMode === 'ongoing'" class="pos-ongoing-view">
      <OngoingOrdersPanel
        :orders="posStore.ongoingOrders"
        @resume="posStore.orderMode = 'new'"
        @kot="openKotForOrder"
        @bill="openBillForOrder"
        @complete="handleComplete"
      />
    </div>

    <!-- ── NEW ORDER VIEW ── -->
    <template v-else>
      <!-- Mobile tab bar -->
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
          <POSProducts :items="menuItems" :categories="menuCategories" @add="posStore.addToCart" />
        </div>
        <div
          v-show="isDesktop || activeTab === 'cart'"
          class="pos-cart-panel"
          :class="{ 'pos-cart-panel--mobile': !isDesktop }"
        >
          <POSCartPanel
            @kot="kotOpen = true; kotOrder = null"
            @bill-preview="billOpen = true; billOrder = null"
            @place-order="handlePlaceOrder"
          />
        </div>
      </div>
    </template>

    <!-- Modals -->
    <KOTModal
      v-if="kotOpen"
      :order="kotOrder"
      @close="kotOpen = false; kotOrder = null"
    />
    <BillPreviewModal
      v-if="billOpen"
      :order="billOrder"
      @close="billOpen = false; billOrder = null"
    />
    <OrderSuccessModal
      v-if="successOpen"
      :bill-no="lastBillNo"
      :total="lastTotal"
      @close="successOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { LayoutGrid, ShoppingCart } from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { menuItems, menuCategories } from '@/utils/mockData.js';
import POSProducts        from './POSProducts.vue';
import POSCartPanel       from './POSCartPanel.vue';
import KOTModal           from './KOTModal.vue';
import BillPreviewModal   from './BillPreviewModal.vue';
import OrderSuccessModal  from './OrderSuccessModal.vue';
import OngoingOrdersPanel from './OngoingOrdersPanel.vue';

const posStore    = usePOSStore();
const kotOpen     = ref(false);
const billOpen    = ref(false);
const successOpen = ref(false);
const lastBillNo  = ref('');
const lastTotal   = ref(0);
const activeTab   = ref('products');
const kotOrder    = ref(null);
const billOrder   = ref(null);

const isDesktop = ref(window.innerWidth >= 1024);
function onResize() { isDesktop.value = window.innerWidth >= 1024; }
onMounted(() => window.addEventListener('resize', onResize));
onUnmounted(() => window.removeEventListener('resize', onResize));

const tabs = [
  { id: 'products', label: 'Products', icon: LayoutGrid },
  { id: 'cart',     label: 'Cart',     icon: ShoppingCart },
];

const activeOngoingCount = computed(() =>
  posStore.ongoingOrders.filter(o => o.status !== 'Completed' && o.status !== 'Cancelled').length
);

function openKotForOrder(order) {
  kotOrder.value = order;
  kotOpen.value  = true;
}

function openBillForOrder(order) {
  billOrder.value = order;
  billOpen.value  = true;
}

function handleComplete(orderId) {
  posStore.completeOrder(orderId);
}

function handlePlaceOrder() {
  lastBillNo.value = `BILL-${2854 + Math.floor(Math.random() * 10)}`;
  lastTotal.value  = posStore.total;
  posStore.completeOrder(posStore.currentOrderId);
  successOpen.value = true;
  activeTab.value   = 'products';
}
</script>
