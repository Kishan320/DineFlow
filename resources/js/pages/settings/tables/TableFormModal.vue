<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.55);overflow-y:auto;" @mousedown.self="$emit('close')">
      <div class="relative w-full max-w-md rounded-xl shadow-2xl border my-auto" style="background:var(--card);border-color:var(--border)" @mousedown.stop>
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">{{ table ? 'Edit Table' : 'New Table' }}</h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)"><XIcon :size="15" /></button>
        </div>
        <div class="overflow-y-auto" style="max-height:calc(100vh - 160px)">
          <form @submit.prevent="submit" class="p-5 space-y-4">

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Table Name <span style="color:var(--danger)">*</span></label>
              <input v-model="form.table_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.table_name ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.table_name" class="text-xs mt-1" style="color:var(--danger)">{{ errors.table_name }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Description <span style="color:var(--danger)">*</span></label>
              <input v-model="form.description" type="text" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.description ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.description" class="text-xs mt-1" style="color:var(--danger)">{{ errors.description }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Maximum Seats <span style="color:var(--danger)">*</span></label>
              <input v-model.number="form.max_seats" type="number" min="1" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" :style="`background:var(--background);border-color:${errors.max_seats ? 'var(--danger)' : 'var(--border)'};color:var(--foreground)`" />
              <p v-if="errors.max_seats" class="text-xs mt-1" style="color:var(--danger)">{{ errors.max_seats }}</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-1 pb-1">
              <button type="button" @click="$emit('close')" class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
              <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">{{ table ? 'Update' : 'Save' }}</button>
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

const props = defineProps({ table: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const schema = yup.object({
  table_name:  yup.string().required('Table name is required').max(100),
  description: yup.string().required('Description is required').max(255),
  max_seats:   yup.number().typeError('Must be a number').required('Max seats is required').min(1, 'At least 1 seat'),
});

const blankForm = () => ({ table_name: '', description: '', max_seats: '' });
const form   = reactive(blankForm());
const errors = reactive({});

watch(() => props.table, (t) => {
  if (t) Object.assign(form, { table_name: t.table_name, description: t.description || '', max_seats: t.max_seats });
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
