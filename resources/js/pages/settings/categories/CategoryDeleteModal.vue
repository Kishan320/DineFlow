<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background:rgba(0,0,0,0.55)"
      @mousedown.self="$emit('close')"
    >
      <div
        class="w-full max-w-sm rounded-xl shadow-2xl border"
        style="background:var(--card);border-color:var(--border)"
        @mousedown.stop
      >
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:color-mix(in srgb, var(--danger) 12%, transparent)">
              <Trash2Icon :size="15" style="color:var(--danger)" />
            </div>
            <h2 class="text-sm font-semibold" style="color:var(--foreground)">Delete Category</h2>
          </div>
          <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
            <XIcon :size="15" />
          </button>
        </div>
        <div class="px-5 py-4">
          <p class="text-sm" style="color:var(--foreground)">
            Are you sure you want to delete
            <span class="font-semibold">{{ category?.categoryName }}</span>?
          </p>
          <p class="text-xs mt-1.5" style="color:var(--muted-foreground)">This action cannot be undone.</p>
        </div>
        <div class="flex items-center justify-end gap-3 px-5 py-4 border-t" style="border-color:var(--border)">
          <button @click="$emit('close')" class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
          <button @click="$emit('confirm')" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--danger);color:#fff">Delete</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { X as XIcon, Trash2 as Trash2Icon } from '@lucide/vue';
defineProps({ category: { type: Object, default: null } });
defineEmits(['close', 'confirm']);
</script>
