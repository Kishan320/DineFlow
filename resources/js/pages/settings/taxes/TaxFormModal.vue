<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background:rgba(0,0,0,0.55);overflow-y:auto;"
      @mousedown.self="$emit('close')"
    >
      <!-- Modal box -->
      <div
        class="relative w-full max-w-lg rounded-xl shadow-2xl border my-auto"
        style="background:var(--card);border-color:var(--border)"
        @mousedown.stop
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">
            {{ tax ? 'Edit Item Tax' : 'New Item Tax' }}
          </h2>
          <button
            @click="$emit('close')"
            class="p-1.5 rounded-lg hover:bg-muted transition-colors"
            style="color:var(--muted-foreground)"
          >
            <XIcon :size="15" />
          </button>
        </div>

        <!-- Scrollable body -->
        <div class="overflow-y-auto" style="max-height:calc(100vh - 160px)">
          <form @submit.prevent="submit" class="p-5 space-y-4">

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                HSN Code <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model="form.hsnCode"
                type="text"
                required
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
                type="text"
                required
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                style="background:var(--background);border-color:var(--border);color:var(--foreground)"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  CGST (%) <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model.number="form.cgst"
                  type="number" step="0.01" min="0" required
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  style="background:var(--background);border-color:var(--border);color:var(--foreground)"
                />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  SGST (%) <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model.number="form.sgst"
                  type="number" step="0.01" min="0" required
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  style="background:var(--background);border-color:var(--border);color:var(--foreground)"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">IGST (%)</label>
              <input
                v-model.number="form.igst"
                type="number" step="0.01" min="0" placeholder="0"
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                style="background:var(--muted);border-color:var(--border);color:var(--muted-foreground)"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  CESS (%) <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model.number="form.cess"
                  type="number" step="0.01" min="0" required placeholder="0"
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  style="background:var(--background);border-color:var(--border);color:var(--foreground)"
                />
              </div>
              <div>
                <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                  Additional CESS (Rs) <span style="color:var(--danger)">*</span>
                </label>
                <input
                  v-model.number="form.additionalCess"
                  type="number" step="0.01" min="0" required
                  class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                  style="background:var(--background);border-color:var(--border);color:var(--foreground)"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">
                VAT (%) <span style="color:var(--danger)">*</span>
              </label>
              <input
                v-model.number="form.vat"
                type="number" step="0.01" min="0" required
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none"
                style="background:var(--background);border-color:var(--border);color:var(--foreground)"
              />
            </div>

            <!-- Total Tax preview -->
            <div class="rounded-lg px-3 py-2.5 flex items-center justify-between" style="background:var(--muted)">
              <span class="text-xs font-medium" style="color:var(--muted-foreground)">Total Tax %</span>
              <span class="text-sm font-bold tabular-nums" style="color:var(--primary)">{{ totalTaxPercent.toFixed(2) }}%</span>
            </div>

            <!-- Footer buttons inside form so submit works -->
            <div class="flex items-center justify-end gap-3 pt-1 pb-1">
              <button
                type="button"
                @click="$emit('close')"
                class="px-5 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted"
                style="border-color:var(--border);color:var(--foreground)"
              >Cancel</button>
              <button
                type="submit"
                class="px-5 py-2 rounded-lg text-sm font-medium transition-colors"
                style="background:var(--primary);color:var(--primary-foreground)"
              >{{ tax ? 'Update' : 'Save' }}</button>
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

const props = defineProps({ tax: { type: Object, default: null } });
const emit  = defineEmits(['close', 'save']);

const blankForm = () => ({ hsnCode: '', description: '', cgst: '', sgst: '', igst: 0, cess: 0, additionalCess: 0, vat: '' });
const form = reactive(blankForm());

watch(() => props.tax, (t) => {
  if (t) Object.assign(form, { hsnCode: t.hsnCode, description: t.description, cgst: t.cgst, sgst: t.sgst, igst: t.igst, cess: t.cess, additionalCess: t.additionalCess, vat: t.vat });
  else   Object.assign(form, blankForm());
}, { immediate: true });

const totalTaxPercent = computed(() =>
  (parseFloat(form.cgst) || 0) +
  (parseFloat(form.sgst) || 0) +
  (parseFloat(form.igst) || 0) +
  (parseFloat(form.cess) || 0) +
  (parseFloat(form.vat)  || 0)
);

function submit() {
  emit('save', { ...form, taxPercent: totalTaxPercent.value });
}
</script>
