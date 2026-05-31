<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.55);overflow-y:auto;" @mousedown.self="$emit('close')">
      <div class="relative w-full max-w-lg rounded-xl shadow-2xl border my-auto" style="background:var(--card);border-color:var(--border)" @mousedown.stop>
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">{{ tax ? 'Edit Tax' : 'New Tax' }}</h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><XIcon :size="15" /></button>
        </div>
        <div class="overflow-y-auto" style="max-height:calc(100vh - 160px)">
          <form @submit.prevent="submit" class="p-5 space-y-4">

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">HSN Code <span style="color:var(--danger)">*</span></label>
              <input v-model="form.hsn_code" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.hsn_code ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.hsn_code" class="text-xs mt-1" style="color:var(--danger)">{{ errors.hsn_code }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Description <span style="color:var(--danger)">*</span></label>
              <input v-model="form.description" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.description ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.description" class="text-xs mt-1" style="color:var(--danger)">{{ errors.description }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">CGST (%) <span style="color:var(--danger)">*</span></label>
                <input v-model.number="form.cgst" type="number" step="0.01" min="0" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.cgst ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
                <p v-if="errors.cgst" class="text-xs mt-1" style="color:var(--danger)">{{ errors.cgst }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">SGST (%) <span style="color:var(--danger)">*</span></label>
                <input v-model.number="form.sgst" type="number" step="0.01" min="0" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.sgst ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
                <p v-if="errors.sgst" class="text-xs mt-1" style="color:var(--danger)">{{ errors.sgst }}</p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">IGST (%)</label>
              <input v-model.number="form.igst" type="number" step="0.01" min="0" placeholder="0" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--muted);border-color:var(--border);color:var(--muted-foreground)" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">CESS (%)</label>
                <input v-model.number="form.cess" type="number" step="0.01" min="0" placeholder="0" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Additional CESS (Rs)</label>
                <input v-model.number="form.additional_cess" type="number" step="0.01" min="0" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">VAT (%) <span style="color:var(--danger)">*</span></label>
              <input v-model.number="form.vat" type="number" step="0.01" min="0" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.vat ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.vat" class="text-xs mt-1" style="color:var(--danger)">{{ errors.vat }}</p>
            </div>

            <div class="rounded-lg px-3 py-2.5 flex items-center justify-between" style="background:var(--muted)">
              <span class="text-xs font-medium" style="color:var(--muted-foreground)">Total Tax %</span>
              <span class="text-sm font-bold tabular-nums" style="color:var(--primary)">{{ totalTaxPercent.toFixed(2) }}%</span>
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
import { reactive, computed, watch } from 'vue';
import { X as XIcon } from '@lucide/vue';
import * as yup from 'yup';

const props = defineProps({ tax: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const schema = yup.object({
  hsn_code:    yup.string().required('HSN code is required').max(50),
  description: yup.string().required('Description is required').max(255),
  cgst:        yup.number().typeError('Required').required('CGST is required').min(0),
  sgst:        yup.number().typeError('Required').required('SGST is required').min(0),
  vat:         yup.number().typeError('Required').required('VAT is required').min(0),
});

const blankForm = () => ({ hsn_code: '', description: '', cgst: '', sgst: '', igst: 0, cess: 0, additional_cess: 0, vat: '' });
const form   = reactive(blankForm());
const errors = reactive({});

watch(() => props.tax, (t) => {
  if (t) Object.assign(form, { hsn_code: t.hsn_code, description: t.description, cgst: t.cgst, sgst: t.sgst, igst: t.igst, cess: t.cess, additional_cess: t.additional_cess, vat: t.vat });
  else   Object.assign(form, blankForm());
  Object.keys(errors).forEach(k => delete errors[k]);
}, { immediate: true });

const totalTaxPercent = computed(() =>
  (parseFloat(form.cgst) || 0) +
  (parseFloat(form.sgst) || 0) +
  (parseFloat(form.igst) || 0) +
  (parseFloat(form.cess) || 0) +
  (parseFloat(form.vat)  || 0)
);

async function submit() {
  Object.keys(errors).forEach(k => delete errors[k]);
  try {
    await schema.validate(form, { abortEarly: false });
    emit('save', { ...form, tax_percent: totalTaxPercent.value });
  } catch (e) {
    e.inner.forEach(err => { errors[err.path] = err.message; });
  }
}
</script>
