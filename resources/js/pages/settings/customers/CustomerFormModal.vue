<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background:rgba(0,0,0,0.55);overflow-y:auto;"
      @mousedown.self="$emit('close')"
    >
      <div
        class="relative w-full max-w-2xl rounded-xl shadow-2xl border my-auto"
        style="background:var(--card);border-color:var(--border)"
        @mousedown.stop
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">
            {{ customer ? 'Edit Customer' : 'Create Customer' }}
          </h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="15" />
          </button>
        </div>

        <!-- Scrollable body -->
        <div class="overflow-y-auto" style="max-height:calc(100vh - 130px)">
          <form @submit.prevent="submit" class="p-5 space-y-5">

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Code</label>
                <input v-model="form.code" type="text" placeholder="e.g. CUST-001" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Company Name <span style="color:var(--danger)">*</span></label>
                <input v-model="form.companyName" type="text" required placeholder="Enter company name" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Contact Person <span style="color:var(--danger)">*</span></label>
                <input v-model="form.contactPerson" type="text" required placeholder="Enter contact person name" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Email <span style="color:var(--danger)">*</span></label>
                <input v-model="form.email" type="email" required placeholder="Enter email address" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Mobile Number</label>
                <input v-model="form.mobile" type="tel" placeholder="+1234567890" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                <p class="text-xs mt-1" style="color:var(--muted-foreground)">Format: +[country code][phone number]</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax Number</label>
                <input v-model="form.taxNumber" type="text" placeholder="Enter tax number" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Payment Terms</label>
                <input v-model="form.paymentTerms" type="text" placeholder="e.g., Net 30" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
            </div>

            <!-- Divider -->
            <div class="border-t" style="border-color:var(--border)"></div>

            <!-- Billing Address -->
            <div>
              <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color:var(--muted-foreground)">Billing Address</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Billing Name <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.billingName" type="text" required placeholder="Enter billing name" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Billing Address <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.billingAddress" type="text" required placeholder="Enter address" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address Line 2</label>
                  <input v-model="form.billingAddress2" type="text" placeholder="Apartment, suite, etc. (optional)" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">City <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.billingCity" type="text" required placeholder="Enter city" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">State <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.billingState" type="text" required placeholder="Enter state" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Country <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.billingCountry" type="text" required placeholder="Enter country" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Zip Code <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.billingZip" type="text" required placeholder="Enter zip code" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
            </div>

            <!-- Divider -->
            <div class="border-t" style="border-color:var(--border)"></div>

            <!-- Shipping same as billing toggle -->
            <div class="flex items-center gap-3">
              <input id="sameAsBilling" v-model="sameAsBilling" type="checkbox" class="w-4 h-4 rounded accent-primary" />
              <label for="sameAsBilling" class="text-sm font-medium cursor-pointer" style="color:var(--foreground)">Shipping address same as billing</label>
            </div>

            <!-- Shipping Address -->
            <div v-if="!sameAsBilling">
              <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color:var(--muted-foreground)">Shipping Address</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Shipping Name <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.shippingName" type="text" :required="!sameAsBilling" placeholder="Enter shipping name" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Shipping Address <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.shippingAddress" type="text" :required="!sameAsBilling" placeholder="Enter shipping address" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address Line 2</label>
                  <input v-model="form.shippingAddress2" type="text" placeholder="Apartment, suite, etc. (optional)" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">City <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.shippingCity" type="text" :required="!sameAsBilling" placeholder="Enter city" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">State <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.shippingState" type="text" :required="!sameAsBilling" placeholder="Enter state" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Country <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.shippingCountry" type="text" :required="!sameAsBilling" placeholder="Enter country" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Zip Code <span style="color:var(--danger)">*</span></label>
                  <input v-model="form.shippingZip" type="text" :required="!sameAsBilling" placeholder="Enter zip code" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
            </div>

            <!-- Divider -->
            <div class="border-t" style="border-color:var(--border)"></div>

            <!-- Notes -->
            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Notes</label>
              <textarea v-model="form.notes" rows="3" placeholder="Additional notes..." class="w-full border rounded-lg px-3 py-2 text-sm outline-none resize-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)"></textarea>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 pt-1 pb-1">
              <button type="button" @click="$emit('close')" class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
              <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">{{ customer ? 'Update' : 'Save' }}</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { X as XIcon } from '@lucide/vue';

const props = defineProps({ customer: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const sameAsBilling = ref(true);

const blankForm = () => ({
  code: '', companyName: '', contactPerson: '', email: '', mobile: '', taxNumber: '', paymentTerms: '',
  billingName: '', billingAddress: '', billingAddress2: '', billingCity: '', billingState: '', billingCountry: '', billingZip: '',
  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '',
  notes: '',
});
const form = reactive(blankForm());

watch(() => props.customer, (c) => {
  if (c) {
    Object.assign(form, { ...blankForm(), ...c });
    sameAsBilling.value = c.sameAsBilling ?? true;
  } else {
    Object.assign(form, blankForm());
    sameAsBilling.value = true;
  }
}, { immediate: true });

function submit() {
  emit('save', { ...form, sameAsBilling: sameAsBilling.value });
}
</script>
