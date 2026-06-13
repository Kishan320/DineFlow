<template>
  <ImageKitProvider :url-endpoint="imagekitUrl" :public-key="imagekitPublicKey">
    <Teleport to="body">
      <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.5)"
        @mousedown.self="$emit('close')"
      >
        <div
          class="w-full max-w-lg max-h-[90vh] flex flex-col rounded-xl shadow-2xl border"
          style="background:var(--card);border-color:var(--border)"
          @mousedown.stop
        >
          <!-- Header -->
          <div class="flex-shrink-0 flex items-center justify-between px-5 py-3 border-b" style="border-color:var(--border)">
            <h2 class="text-sm font-semibold" style="color:var(--foreground)">Item Details</h2>
            <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
              <XIcon :size="15" />
            </button>
          </div>

          <!-- Body -->
          <div class="p-5 overflow-y-auto">
            <div v-if="loading" class="flex items-center justify-center py-8">
              <div class="animate-spin" style="color:var(--primary)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
            </div>
            <div v-else-if="!item" class="text-center py-8">
              <p style="color:var(--muted-foreground)">Item not found</p>
            </div>
            <template v-else>
              <h3 class="text-base font-semibold mb-4" style="color:var(--foreground)">{{ item.item_name }}</h3>

              <div class="flex flex-col sm:flex-row gap-5 sm:gap-6 items-center sm:items-start">
                <!-- Image -->
                <div class="flex-shrink-0 w-32 h-32 sm:w-28 sm:h-28 rounded-lg border flex flex-col items-center justify-center overflow-hidden" style="background:var(--muted);border-color:var(--border)">
                  <IKImage v-if="item.image_url" :src="item.image_url" class="w-full h-full object-cover" />
                  <template v-else>
                    <ImageOffIcon :size="28" style="color:var(--muted-foreground)" />
                    <p class="text-xs mt-1 text-center font-medium" style="color:var(--muted-foreground)">NO IMAGE<br>AVAILABLE</p>
                  </template>
                </div>

                <!-- Details grid -->
                <div class="flex-1 w-full grid grid-cols-2 gap-x-4 gap-y-3 text-xs">
                  <div>
                    <p style="color:var(--muted-foreground)">Item Code</p>
                    <p class="font-semibold mt-0.5" style="color:var(--primary)">{{ item.code }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Restaurant Sales Price</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">Rs.{{ parseFloat(item.restaurant_price).toFixed(2) }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Category</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.category }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Bar Sales Price</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">Rs.{{ parseFloat(item.bar_price).toFixed(2) }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Item State</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.state }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Room Sales Price</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">Rs.{{ parseFloat(item.room_price).toFixed(2) }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Item Type</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.item_type }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Tax</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.tax }}</p>
                  </div>
                  <div>
                    <p style="color:var(--muted-foreground)">Tax Type</p>
                    <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.tax_type }}</p>
                  </div>
                </div>
              </div>

              <!-- Footer meta -->
              <div class="mt-4 pt-3 border-t flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-center sm:text-left" style="border-color:var(--border);color:var(--muted-foreground)">
                <span>Created on {{ item.created_at ? new Date(item.created_at).toLocaleString('en-IN') : 'N/A' }}</span>
                <span>Last Accessed by : {{ item.last_accessed_by }}</span>
              </div>
            </template>
          </div>
        </div>
      </div>
    </Teleport>
  </ImageKitProvider>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { X as XIcon, ImageOff as ImageOffIcon } from '@lucide/vue';
import { itemApi } from '@/services/settingsApi';
import { ImageKitProvider, Image as IKImage } from '@imagekit/vue';

const props = defineProps({ itemId: { type: Number, required: true } });
defineEmits(['close']);

const imagekitUrl = import.meta.env.VITE_IMAGEKIT_URL_ENDPOINT || 'https://ik.imagekit.io/dineflowimages';
const imagekitPublicKey = import.meta.env.VITE_IMAGEKIT_PUBLIC_KEY || 'public_pD9H64ZwHoNJiHtBSQcedPGbiJw=';

const item = ref(null);
const loading = ref(false);

onMounted(async () => {
  loading.value = true;
  try {
    const res = await itemApi.getOne(props.itemId);
    item.value = res.data || res;
  } finally {
    loading.value = false;
  }
});
</script>
