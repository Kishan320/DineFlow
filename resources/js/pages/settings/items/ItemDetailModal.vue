<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background:rgba(0,0,0,0.5)"
      @mousedown.self="$emit('close')"
    >
      <div
        class="w-full max-w-lg rounded-xl shadow-2xl border"
        style="background:var(--card);border-color:var(--border)"
        @mousedown.stop
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-3 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">Item Details</h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="15" />
          </button>
        </div>

        <!-- Body -->
        <div class="p-5">
          <h3 class="text-base font-semibold mb-4" style="color:var(--foreground)">{{ item.itemName }}</h3>

          <div class="flex gap-5">
            <!-- Image -->
            <div class="flex-shrink-0 w-28 h-28 rounded-lg border flex flex-col items-center justify-center" style="background:var(--muted);border-color:var(--border)">
              <img v-if="item.imageUrl" :src="item.imageUrl" class="w-full h-full object-cover rounded-lg" />
              <template v-else>
                <ImageOffIcon :size="28" style="color:var(--muted-foreground)" />
                <p class="text-xs mt-1 text-center font-medium" style="color:var(--muted-foreground)">NO IMAGE<br>AVAILABLE</p>
              </template>
            </div>

            <!-- Details grid -->
            <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-2.5 text-xs">
              <div>
                <p style="color:var(--muted-foreground)">Item Code</p>
                <p class="font-semibold mt-0.5" style="color:var(--primary)">{{ item.code }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Restaurant Sales Price</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">Rs.{{ item.restaurantPrice.toFixed(2) }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Category</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.category }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Bar Sales Price</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">Rs.{{ item.barPrice.toFixed(2) }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Item State</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.state }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Room Sales Price</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">Rs.{{ item.roomPrice.toFixed(2) }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Item Type</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.itemType }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Tax</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.tax }}</p>
              </div>
              <div>
                <p style="color:var(--muted-foreground)">Tax Type</p>
                <p class="font-medium mt-0.5" style="color:var(--foreground)">{{ item.taxType }}</p>
              </div>
            </div>
          </div>

          <!-- Footer meta -->
          <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs" style="border-color:var(--border);color:var(--muted-foreground)">
            <span>Created by : Admin on {{ item.createdAt }}</span>
            <span>Last Accessed by : Admin on {{ item.lastAccessedAt }}</span>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { X as XIcon, ImageOff as ImageOffIcon } from '@lucide/vue';
defineProps({ item: { type: Object, required: true } });
defineEmits(['close']);
</script>
