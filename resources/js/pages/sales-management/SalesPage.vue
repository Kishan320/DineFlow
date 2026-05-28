<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Sales Management</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Work Area · Sales · All transactions today</p>
      </div>
    </div>
    <SalesTable />
  </div>
</template>

<script setup>
import SalesTable from './SalesTable.vue';
</script>
