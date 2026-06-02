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
            :disabled="loading"
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
              :disabled="loading"
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
              :disabled="loading"
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
              :disabled="loading"
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
              :disabled="loading"
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
            :disabled="loading"
            class="px-5 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            style="background:var(--primary);color:var(--primary-foreground)"
          >
            {{ loading ? 'Saving...' : 'Update' }}
          </button>
          <button
            @click="handleCancel"
            :disabled="loading"
            class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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
import { ref, onMounted } from 'vue';
import { restaurantSettingsApi } from '@/services/settingsApi';
import { toast } from 'vue-sonner';

const loading = ref(false);
const initialForm = ref(null);

const form = ref({
  businessUnit: 'Dak Trading India Opc Private Limited',
  restaurantName: '',
  gstNo: '',
  billFooterText: '',
  guestBill: 'Disabled',
});

async function loadSettings() {
  loading.value = true;
  try {
    const response = await restaurantSettingsApi.get();
    if (response.data) {
      form.value = {
        businessUnit: response.data.business_unit,
        restaurantName: response.data.restaurant_name,
        gstNo: response.data.gst_no,
        billFooterText: response.data.bill_footer_text,
        guestBill: response.data.guest_bill,
      };
    }
    initialForm.value = JSON.stringify(form.value);
  } catch (error) {
    toast.error('Failed to load restaurant settings');
  } finally {
    loading.value = false;
  }
}

async function handleUpdate() {
  loading.value = true;
  try {
    const payload = {
      business_unit: form.value.businessUnit,
      restaurant_name: form.value.restaurantName,
      gst_no: form.value.gstNo,
      bill_footer_text: form.value.billFooterText,
      guest_bill: form.value.guestBill,
    };

    await restaurantSettingsApi.update(payload);
    toast.success('Restaurant settings updated successfully!');
    initialForm.value = JSON.stringify(form.value);
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to update restaurant settings');
  } finally {
    loading.value = false;
  }
}

function handleCancel() {
  if (initialForm.value) {
    form.value = JSON.parse(initialForm.value);
  }
}

onMounted(loadSettings);
</script>
