<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Restaurant Settings</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Restaurant Settings</p>
      </div>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Restaurant Settings</h2>
      </div>

      <div class="p-6 space-y-5">
        <!-- Business Management Unit -->
        <div>
          <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
            Business Management Unit <span style="color:var(--danger)">*</span>
          </label>
          <select
            v-model="form.businessUnit"
            class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
            style="background:var(--muted);border-color:var(--border);color:var(--foreground)"
          >
            <option value="Dak Trading India Opc Private Limited">Dak Trading India Opc Private Limited</option>
            <option value="Ayyan Multi Cuisine Restaurant">Ayyan Multi Cuisine Restaurant</option>
          </select>
        </div>

        <!-- Row: Restaurant Name + GST No -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
              Restaurant Name In Print <span style="color:var(--danger)">*</span>
            </label>
            <input
              v-model="form.restaurantName"
              type="text"
              class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
              style="background:var(--muted);border-color:var(--border);color:var(--foreground)"
            />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
              GST No. <span style="color:var(--danger)">*</span>
            </label>
            <input
              v-model="form.gstNo"
              type="text"
              class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
              style="background:var(--muted);border-color:var(--border);color:var(--foreground)"
            />
          </div>
        </div>

        <!-- Row: Bill Footer Text + Guest Bill -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
              Bill Footer Text <span style="color:var(--danger)">*</span>
            </label>
            <input
              v-model="form.billFooterText"
              type="text"
              class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
              style="background:var(--muted);border-color:var(--border);color:var(--foreground)"
            />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
              Guest Bill <span style="color:var(--danger)">*</span>
            </label>
            <select
              v-model="form.guestBill"
              class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
              style="background:var(--muted);border-color:var(--border);color:var(--foreground)"
            >
              <option value="Disabled">Disabled</option>
              <option value="Enabled">Enabled</option>
            </select>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-2">
          <button
            @click="handleUpdate"
            class="px-5 py-2 rounded-lg text-sm font-medium transition-colors"
            style="background:var(--primary);color:var(--primary-foreground)"
          >
            Update
          </button>
          <button
            @click="handleCancel"
            class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors"
            style="border-color:var(--border);color:var(--foreground);background:var(--muted)"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const form = ref({
  businessUnit: 'Dak Trading India Opc Private Limited',
  restaurantName: 'DAK TRADING INDIA PVT LTD <br> AYYAN MULTI-CUISINE RESTAURANT',
  gstNo: '33AAGCD1607B1ZF',
  billFooterText: 'THANK YOU! VISIT AGAIN!!',
  guestBill: 'Disabled',
});

function handleUpdate() {
  alert('Settings updated successfully!');
}

function handleCancel() {
  form.value = {
    businessUnit: 'Dak Trading India Opc Private Limited',
    restaurantName: 'DAK TRADING INDIA PVT LTD <br> AYYAN MULTI-CUISINE RESTAURANT',
    gstNo: '33AAGCD1607B1ZF',
    billFooterText: 'THANK YOU! VISIT AGAIN!!',
    guestBill: 'Disabled',
  };
}
</script>
