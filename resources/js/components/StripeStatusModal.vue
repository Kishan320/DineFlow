<template>
  <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 fade-in backdrop-blur-sm">
    <div class="border rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col slide-up bg-card border-border">
      
      <!-- Header Area -->
      <div class="flex flex-col items-center pt-8 pb-4 px-6 text-center">
        <!-- Icons -->
        <div v-if="status === 'success'" class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4 shadow-sm">
          <CheckCircleIcon class="w-8 h-8 text-green-600" />
        </div>
        <div v-else-if="status === 'cancelled'" class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mb-4 shadow-sm">
          <AlertCircleIcon class="w-8 h-8 text-amber-500" />
        </div>
        <div v-else class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4 shadow-sm">
          <XCircleIcon class="w-8 h-8 text-red-600" />
        </div>

        <h3 class="text-xl font-bold text-foreground">
          {{ title }}
        </h3>
        <p class="text-sm text-muted-foreground mt-1">
          {{ description }}
        </p>
      </div>

      <!-- Order Details -->
      <div class="px-6 py-4 bg-muted/30 border-y border-border">
        <h4 class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-3">Order Details</h4>
        
        <div class="space-y-2 text-sm">
          <div v-if="orderData?.order_no" class="flex justify-between">
            <span class="text-muted-foreground">Order No:</span>
            <span class="font-semibold text-foreground">{{ orderData.order_no }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-muted-foreground">Order Type:</span>
            <span class="font-medium text-foreground">{{ orderType }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-muted-foreground">Items:</span>
            <span class="font-medium text-foreground">{{ itemCount }} items</span>
          </div>
          <div class="flex justify-between pt-2 mt-2 border-t border-border/50">
            <span class="font-bold text-foreground">Total Amount:</span>
            <span class="font-bold text-primary">{{ formatCurrency(totalAmount) }}</span>
          </div>
        </div>
      </div>

      <!-- Footer Action -->
      <div class="p-5 flex gap-3">
        <a 
          v-if="status === 'success' && orderData?.receipt_url"
          :href="orderData.receipt_url"
          target="_blank"
          download
          class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all text-center bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/20 active:scale-[0.98]"
        >
          Download PDF Bill
        </a>
        <button 
          @click="$emit('close')" 
          class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all active:scale-[0.98]"
          :class="buttonClass"
        >
          {{ buttonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { CheckCircle as CheckCircleIcon, XCircle as XCircleIcon, AlertCircle as AlertCircleIcon } from '@lucide/vue';

const props = defineProps({
  status: { type: String, required: true }, // 'success', 'cancelled', 'failed'
  orderData: { type: Object, default: null }, // When successful
  cartData: { type: Object, default: null } // When cancelled/failed
});

defineEmits(['close']);

const title = computed(() => {
  if (props.status === 'success') return 'Payment Successful';
  if (props.status === 'cancelled') return 'Payment Cancelled';
  return 'Payment Failed';
});

const description = computed(() => {
  if (props.status === 'success') return 'Your order has been successfully placed.';
  if (props.status === 'cancelled') return 'You cancelled the payment process.';
  return 'There was an issue verifying your payment. Please try again.';
});

const buttonClass = computed(() => {
  if (props.status === 'success') return 'bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-600/20';
  if (props.status === 'cancelled') return 'bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/20';
  return 'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-600/20';
});

const buttonText = computed(() => {
  if (props.status === 'success') return 'View Sales';
  if (props.status === 'cancelled') return 'Return to POS';
  return 'Close';
});

// Resolving data gracefully whether it comes from the created Order or the Cart State
const orderType = computed(() => {
  return props.orderData?.order_type || props.cartData?.billType || 'Dine In';
});

const itemCount = computed(() => {
  if (props.orderData?.items) return props.orderData.items.reduce((s, i) => s + i.quantity, 0);
  return props.cartData?.itemCount || 0;
});

const totalAmount = computed(() => {
  return parseFloat(props.orderData?.net_payable || props.cartData?.netPayable || 0);
});

function formatCurrency(val) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}
</script>

<style scoped>
.fade-in { animation: fadeIn 0.2s ease-out forwards; }
.slide-up { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
.bg-card { background-color: var(--card); }
.border-border { border-color: var(--border); }
.text-foreground { color: var(--foreground); }
.text-muted-foreground { color: var(--muted-foreground); }
.bg-muted { background-color: var(--muted); }
.text-primary { color: var(--primary); }
</style>
