<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Sales Management</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Work Area · Sales · All transactions today</p>
      </div>
    </div>
    <SalesTable v-if="!verifying" />
    <div v-else class="flex flex-col items-center justify-center py-20 text-center">
      <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4"></div>
      <h2 class="text-xl font-bold text-foreground">Verifying Payment...</h2>
      <p class="text-muted-foreground mt-2">Please wait while we verify your Stripe payment and create your order.</p>
    </div>
    <StripeStatusModal
      v-if="stripeStatus"
      :status="stripeStatus"
      :orderData="createdOrder"
      :cartData="{ itemCount: posStore.itemCount, netPayable: posStore.netPayable, billType: posStore.billType }"
      @close="closeStripeModal"
    />
  </div>
</template>

<script setup>
import SalesTable from './SalesTable.vue';
import StripeStatusModal from '@/components/StripeStatusModal.vue';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { posApi } from '@/services/settingsApi.js';
import { usePOSStore } from '@/stores/posStore.js';
import { useSnackbar } from '@/composables/useSnackbar';

const route = useRoute();
const router = useRouter();
const posStore = usePOSStore();
const snackbar = useSnackbar();
const verifying = ref(false);
const stripeStatus = ref(null);
const createdOrder = ref(null);

function closeStripeModal() {
  stripeStatus.value = null;
}

onMounted(async () => {
  const sessionId = route.query.session_id;
  if (sessionId) {
    verifying.value = true;
    try {
      const res = await posApi.verifyStripePayment(sessionId);
      if (res && res.success) {
        // Payment successful, map to Card / Stripe
        posStore.billPayType = 'Card';
        posStore.cardRef = res.payment_intent_id;
        posStore.cardAmt = posStore.netPayable;
        
        // Place the order
        const order = await posStore.placeOrder();
        
        // Attach receipt_url if available so the modal can show the download button
        if (res.receipt_url) {
          order.receipt_url = res.receipt_url;
        }
        
        createdOrder.value = order;
        stripeStatus.value = 'success';
        
        // Clear the query param so it doesn't trigger again on reload
        router.replace({ path: '/sales-management' });
      } else {
        stripeStatus.value = 'failed';
        router.replace({ path: '/sales-management' });
      }
    } catch (err) {
      stripeStatus.value = 'failed';
      router.replace({ path: '/sales-management' });
    } finally {
      verifying.value = false;
    }
  }
});
</script>
