<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.55);overflow-y:auto;" @mousedown.self="$emit('close')">
      <div class="relative w-full max-w-md rounded-xl shadow-2xl border my-auto" style="background:var(--card);border-color:var(--border)" @mousedown.stop>
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">{{ waiter ? 'Edit Waiter' : 'New Waiter' }}</h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><XIcon :size="15" /></button>
        </div>
        <div class="overflow-y-auto" style="max-height:calc(100vh - 160px)">
          <form @submit.prevent="submit" class="p-5 space-y-4">

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Waiter Code <span style="color:var(--danger)">*</span></label>
              <input v-model="form.waiter_code" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.waiter_code ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.waiter_code" class="text-xs mt-1" style="color:var(--danger)">{{ errors.waiter_code }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Waiter Name <span style="color:var(--danger)">*</span></label>
              <input v-model="form.name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.name ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.name" class="text-xs mt-1" style="color:var(--danger)">{{ errors.name }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Mobile <span style="color:var(--danger)">*</span></label>
              <input v-model="form.mobile" type="tel" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.mobile ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.mobile" class="text-xs mt-1" style="color:var(--danger)">{{ errors.mobile }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Date of Birth</label>
              <input v-model="form.dob" type="date" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-1 pb-1">
              <button type="button" @click="$emit('close')" class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
              <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">{{ waiter ? 'Update' : 'Save' }}</button>
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

const props = defineProps({ waiter: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const schema = yup.object({
  waiter_code: yup.string().required('Waiter code is required').max(50),
  name:        yup.string().required('Name is required').max(255),
  mobile:      yup.string().required('Mobile is required').matches(/^[0-9+\-\s]{7,20}$/, 'Invalid mobile number'),
  dob:         yup.string().nullable(),
});

const blankForm = () => ({ waiter_code: '', name: '', mobile: '', dob: '' });
const form   = reactive(blankForm());
const errors = reactive({});

watch(() => props.waiter, (w) => {
  if (w) Object.assign(form, { waiter_code: w.waiter_code, name: w.name, mobile: w.mobile, dob: w.dob || '' });
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
