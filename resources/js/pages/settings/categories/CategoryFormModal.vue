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
            {{ category ? 'Edit Category' : 'New / Edit Category' }}
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
                Category Name <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model="form.categoryName"
                type="text" required
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                style="background:var(--background);border-color:var(--border);color:var(--foreground)"
              />
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Description</label>
              <input
                v-model="form.description"
                type="text"
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
              >{{ category ? 'Update' : 'Save' }}</button>
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

const props = defineProps({ category: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const blankForm = () => ({ categoryName: '', description: '' });
const form = reactive(blankForm());

watch(() => props.category, (c) => {
  if (c) Object.assign(form, { categoryName: c.categoryName, description: c.description });
  else   Object.assign(form, blankForm());
}, { immediate: true });

function submit() {
  emit('save', { ...form });
}
</script>
