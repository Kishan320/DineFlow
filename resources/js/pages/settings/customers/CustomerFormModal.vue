<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.55);overflow-y:auto;" @mousedown.self="$emit('close')">
      <div class="relative w-full max-w-2xl rounded-xl shadow-2xl border my-auto" style="background:var(--card);border-color:var(--border)" @mousedown.stop>
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">{{ customer ? 'Edit Customer' : 'Create Customer' }}</h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><XIcon :size="15" /></button>
        </div>

        <div class="overflow-y-auto" style="max-height:calc(100vh - 130px)">
          <form @submit.prevent="submit" class="p-5 space-y-5">

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Code <span style="color:var(--danger)">*</span></label>
                <input v-model="form.code" type="text" placeholder="e.g. CUST-001" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.code ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
                <p v-if="errors.code" class="text-xs mt-1" style="color:var(--danger)">{{ errors.code }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Company Name <span style="color:var(--danger)">*</span></label>
                <input v-model="form.company_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.company_name ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
                <p v-if="errors.company_name" class="text-xs mt-1" style="color:var(--danger)">{{ errors.company_name }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Contact Person</label>
                <input v-model="form.contact_person" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Email</label>
                <input v-model="form.email" type="email" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.email ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
                <p v-if="errors.email" class="text-xs mt-1" style="color:var(--danger)">{{ errors.email }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Mobile</label>
                <input v-model="form.mobile" type="tel" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax Number</label>
                <input v-model="form.tax_number" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Payment Terms</label>
                <input v-model="form.payment_terms" type="text" placeholder="e.g. Net 30" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
            </div>

            <div class="border-t" style="border-color:var(--border)"></div>

            <!-- Billing Address -->
            <div>
              <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color:var(--muted-foreground)">Billing Address</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Billing Name</label>
                  <input v-model="form.billing_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Billing Address</label>
                  <input v-model="form.billing_address" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address Line 2</label>
                  <input v-model="form.billing_address2" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">City</label>
                  <input v-model="form.billing_city" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">State</label>
                  <input v-model="form.billing_state" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Country</label>
                  <input v-model="form.billing_country" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Zip Code</label>
                  <input v-model="form.billing_zip" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
            </div>

            <div class="border-t" style="border-color:var(--border)"></div>

            <div class="flex items-center gap-3">
              <input id="sameAsBilling" v-model="form.same_as_billing" type="checkbox" class="w-4 h-4 rounded accent-primary" />
              <label for="sameAsBilling" class="text-sm font-medium cursor-pointer" style="color:var(--foreground)">Shipping address same as billing</label>
            </div>

            <!-- Shipping Address -->
            <div v-if="!form.same_as_billing">
              <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color:var(--muted-foreground)">Shipping Address</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Shipping Name</label>
                  <input v-model="form.shipping_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Shipping Address</label>
                  <input v-model="form.shipping_address" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address Line 2</label>
                  <input v-model="form.shipping_address2" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">City</label>
                  <input v-model="form.shipping_city" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">State</label>
                  <input v-model="form.shipping_state" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Country</label>
                  <input v-model="form.shipping_country" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Zip Code</label>
                  <input v-model="form.shipping_zip" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
            </div>

            <div class="border-t" style="border-color:var(--border)"></div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Notes</label>
              <textarea v-model="form.notes" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm outline-none resize-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)"></textarea>
            </div>

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
import { reactive, watch } from 'vue';
import { X as XIcon } from '@lucide/vue';
import * as yup from 'yup';

const props = defineProps({ customer: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const schema = yup.object({
  code:         yup.string().required('Code is required').max(50),
  company_name: yup.string().required('Company name is required').max(255),
  email:        yup.string().email('Invalid email').nullable(),
});

const blankForm = () => ({
  code: '', company_name: '', contact_person: '', email: '', mobile: '', tax_number: '', payment_terms: '',
  billing_name: '', billing_address: '', billing_address2: '', billing_city: '', billing_state: '', billing_country: '', billing_zip: '',
  same_as_billing: true,
  shipping_name: '', shipping_address: '', shipping_address2: '', shipping_city: '', shipping_state: '', shipping_country: '', shipping_zip: '',
  notes: '',
});

const form   = reactive(blankForm());
const errors = reactive({});

watch(() => props.customer, (c) => {
  if (c) Object.assign(form, { ...blankForm(), ...c });
  else   Object.assign(form, blankForm());
  Object.keys(errors).forEach(k => delete errors[k]);
}, { immediate: true });

async function submit() {
  Object.keys(errors).forEach(k => delete errors[k]);
  try {
    await schema.validate(form, { abortEarly: false });
    emit('save', { ...form });
  } catch (e) {
    e.inner.forEach(err => { errors[err.path] = err.message; });
  }
}
</script>
