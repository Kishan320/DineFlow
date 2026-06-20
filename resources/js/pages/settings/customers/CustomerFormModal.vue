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
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">
            {{ customer ? 'Edit Customer' : 'New Customer' }}
          </h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="15" />
          </button>
        </div>

        <div class="overflow-y-auto" style="max-height:calc(100vh - 160px)">
          <form @submit.prevent="submit" class="p-5 space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  Customer Code <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model="form.code"
                  type="text"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  :style="`background:var(--background);border-color:${errors.code ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
                />
                <p v-if="errors.code" class="text-xs mt-1" style="color:var(--danger)">{{ errors.code }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  Company Name <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model="form.company_name"
                  type="text"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  :style="`background:var(--background);border-color:${errors.company_name ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
                />
                <p v-if="errors.company_name" class="text-xs mt-1" style="color:var(--danger)">{{ errors.company_name }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Contact Person</label>
                <input v-model="form.contact_person" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  Email <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  :style="`background:var(--background);border-color:${errors.email ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
                />
                <p v-if="errors.email" class="text-xs mt-1" style="color:var(--danger)">{{ errors.email }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  Password <span v-if="!customer" style="color:var(--danger)">*</span>
                  <span v-else class="text-[10px] text-gray-400 ml-1">(Leave empty to keep current)</span>
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  :style="`background:var(--background);border-color:${errors.password ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
                />
                <p v-if="errors.password" class="text-xs mt-1" style="color:var(--danger)">{{ errors.password }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Mobile</label>
                <input v-model="form.mobile" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax Number</label>
                <input v-model="form.tax_number" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Payment Terms</label>
                <input v-model="form.payment_terms" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
            </div>

            <!-- Billing Address -->
            <div class="pt-2">
              <h3 class="text-xs font-semibold mb-3" style="color:var(--foreground)">Billing Address</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Name</label>
                  <input v-model="form.billing_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address</label>
                  <input v-model="form.billing_address" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address 2</label>
                  <input v-model="form.billing_address2" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">City</label>
                  <input v-model="form.billing_city" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">State</label>
                  <input v-model="form.billing_state" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Country</label>
                  <input v-model="form.billing_country" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Zip</label>
                  <input v-model="form.billing_zip" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
            </div>

            <!-- Same as Billing -->
            <div class="flex items-center gap-2">
              <input v-model="form.same_as_billing" type="checkbox" id="same_as_billing" class="w-4 h-4 rounded" style="border-color:var(--border)" />
              <label for="same_as_billing" class="text-xs" style="color:var(--foreground)">Shipping same as billing</label>
            </div>

            <!-- Shipping Address (conditional) -->
            <div v-if="!form.same_as_billing" class="pt-2">
              <h3 class="text-xs font-semibold mb-3" style="color:var(--foreground)">Shipping Address</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Name</label>
                  <input v-model="form.shipping_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address</label>
                  <input v-model="form.shipping_address" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Address 2</label>
                  <input v-model="form.shipping_address2" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">City</label>
                  <input v-model="form.shipping_city" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">State</label>
                  <input v-model="form.shipping_state" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Country</label>
                  <input v-model="form.shipping_country" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Zip</label>
                  <input v-model="form.shipping_zip" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
                </div>
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Notes</label>
              <textarea v-model="form.notes" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" rows="3"></textarea>
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
  code:              yup.string().required('Code is required').max(50),
  company_name:      yup.string().required('Company name is required').max(255),
  contact_person:    yup.string().nullable().max(255),
  email:             yup.string().required('Email is required for user account').email().max(255),
  password:          yup.string().nullable().test(
    'password-required',
    'Password is required (min 6 characters) for new customers',
    function (value) {
      if (!props.customer) {
        return !!value && value.length >= 6;
      }
      return !value || value.length >= 6;
    }
  ),
  mobile:            yup.string().nullable().max(20),
  tax_number:        yup.string().nullable().max(100),
  payment_terms:     yup.string().nullable().max(50),
  billing_name:      yup.string().nullable().max(255),
  billing_address:   yup.string().nullable().max(500),
  billing_address2:  yup.string().nullable().max(500),
  billing_city:      yup.string().nullable().max(100),
  billing_state:     yup.string().nullable().max(100),
  billing_country:   yup.string().nullable().max(100),
  billing_zip:       yup.string().nullable().max(20),
  same_as_billing:   yup.boolean(),
  shipping_name:     yup.string().nullable().max(255),
  shipping_address:  yup.string().nullable().max(500),
  shipping_address2: yup.string().nullable().max(500),
  shipping_city:     yup.string().nullable().max(100),
  shipping_state:    yup.string().nullable().max(100),
  shipping_country:  yup.string().nullable().max(100),
  shipping_zip:      yup.string().nullable().max(20),
  notes:             yup.string().nullable(),
});

const blankForm = () => ({
  user_id: null,
  code: '', company_name: '', contact_person: '', email: '', password: '', mobile: '', tax_number: '',
  payment_terms: '', billing_name: '', billing_address: '', billing_address2: '',
  billing_city: '', billing_state: '', billing_country: '', billing_zip: '',
  same_as_billing: true, shipping_name: '', shipping_address: '', shipping_address2: '',
  shipping_city: '', shipping_state: '', shipping_country: '', shipping_zip: '', notes: '',
});

const form   = reactive(blankForm());
const errors = reactive({});

watch(() => props.customer, (c) => {
  if (c) {
    const blank = blankForm();
    Object.keys(blank).forEach(key => {
      form[key] = (c[key] !== null && c[key] !== undefined) ? c[key] : blank[key];
    });
  } else {
    Object.assign(form, blankForm());
  }
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
