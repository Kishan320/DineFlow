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
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">
            {{ table ? 'Edit Restaurant Table' : 'New / Edit Restaurant Table' }}
          </h2>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="15" />
          </button>
        </div>

        <!-- Form -->
        <div class="overflow-y-auto" style="max-height:calc(100vh - 160px)">
          <form @submit.prevent="submit" class="p-5 space-y-4">

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                Table name <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model="form.tableName"
                type="text" required
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                style="background:var(--background);border-color:var(--border);color:var(--foreground)"
              />
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                Description <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model="form.description"
                type="text" required
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                style="background:var(--background);border-color:var(--border);color:var(--foreground)"
              />
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                Maximum Seats <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model.number="form.maxSeats"
                type="number" min="1" required
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                style="background:var(--background);border-color:var(--border);color:var(--foreground)"
              />
            </div>

            <div class="flex items-center justify-end gap-3 pt-1 pb-1">
              <button
                type="button" @click="$emit('close')"
                class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted"
                style="border-color:var(--border);color:var(--foreground)"
              >Cancel</button>
              <button
                type="submit"
                class="px-5 py-2 rounded-lg text-sm font-medium transition-colors"
                style="background:var(--primary);color:var(--primary-foreground)"
              >{{ table ? 'Update' : 'Save' }}</button>
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

const props = defineProps({ table: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const blankForm = () => ({ tableName: '', description: '', maxSeats: '' });
const form = reactive(blankForm());

watch(() => props.table, (t) => {
  if (t) Object.assign(form, { tableName: t.tableName, description: t.description, maxSeats: t.maxSeats });
  else   Object.assign(form, blankForm());
}, { immediate: true });

function submit() {
  emit('save', { ...form });
}
</script>
