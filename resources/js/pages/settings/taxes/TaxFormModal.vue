<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background:rgba(0,0,0,0.55);overflow-y:auto;"
      @mousedown.self="$emit('close')"
    >
      <div
        class="relative w-full max-w-md rounded-xl shadow-2xl border my-auto"
        style="background:var(--card);border-color:var(--border)"
        @mousedown.stop
      >
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">
            {{ tax ? 'Edit Tax' : 'New Tax' }}
          </h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="15" />
          </button>
        </div>

        <div class="overflow-y-auto" style="max-height:calc(100vh - 160px)">
          <form @submit.prevent="submit" class="p-5 space-y-4">

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                HSN Code <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model="form.hsn_code"
                type="text"
                placeholder="e.g., 1211"
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                :style="`background:var(--background);border-color:${errors.hsn_code ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
              />
              <p v-if="errors.hsn_code" class="text-xs mt-1" style="color:var(--danger)">{{ errors.hsn_code }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                Description <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model="form.description"
                type="text"
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                :style="`background:var(--background);border-color:${errors.description ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
              />
              <p v-if="errors.description" class="text-xs mt-1" style="color:var(--danger)">{{ errors.description }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  CGST (%) <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model.number="form.cgst"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  :style="`background:var(--background);border-color:${errors.cgst ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
                />
                <p v-if="errors.cgst" class="text-xs mt-1" style="color:var(--danger)">{{ errors.cgst }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  SGST (%) <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model.number="form.sgst"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  :style="`background:var(--background);border-color:${errors.sgst ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
                />
                <p v-if="errors.sgst" class="text-xs mt-1" style="color:var(--danger)">{{ errors.sgst }}</p>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">IGST (%)</label>
                <input
                  v-model.number="form.igst"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  style="background:var(--background);border-color:var(--border);color:var(--foreground)"
                />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Cess (%)</label>
                <input
                  v-model.number="form.cess"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  style="background:var(--background);border-color:var(--border);color:var(--foreground)"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Additional Cess (%)</label>
                <input
                  v-model.number="form.additional_cess"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  style="background:var(--background);border-color:var(--border);color:var(--foreground)"
                />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">VAT (%)</label>
                <input
                  v-model.number="form.vat"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  :style="`background:var(--background);border-color:${errors.vat ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
                />
                <p v-if="errors.vat" class="text-xs mt-1" style="color:var(--danger)">{{ errors.vat }}</p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                Tax Percent (%) <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model.number="form.tax_percent"
                type="number"
                min="0"
                step="0.01"
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                :style="`background:var(--background);border-color:${errors.tax_percent ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`"
              />
              <p v-if="errors.tax_percent" class="text-xs mt-1" style="color:var(--danger)">{{ errors.tax_percent }}</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-1 pb-1">
              <button type="button" @click="$emit('close')" class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
              <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">{{ tax ? 'Update' : 'Save' }}</button>
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

const props = defineProps({ tax: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const schema = yup.object({
  hsn_code:        yup.string().required('HSN code is required').max(50),
  description:     yup.string().required('Description is required').max(255),
  cgst:            yup.number().required('CGST is required').min(0),
  sgst:            yup.number().required('SGST is required').min(0),
  igst:            yup.number().nullable().min(0),
  cess:            yup.number().nullable().min(0),
  additional_cess: yup.number().nullable().min(0),
  vat:             yup.number().required('VAT is required').min(0),
  tax_percent:     yup.number().required('Tax percent is required').min(0),
});

const blankForm = () => ({
  hsn_code: '', description: '', cgst: 0, sgst: 0, igst: 0, cess: 0,
  additional_cess: 0, vat: 0, tax_percent: 0,
});

const form   = reactive(blankForm());
const errors = reactive({});

watch(() => props.tax, (t) => {
  if (t) Object.assign(form, t);
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
