<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Item Category List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Item Categories</p>
      </div>
      <button @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)">
        <PlusIcon :size="15" /> Add Category
      </button>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Category List</h2>
      </div>

      <!-- Controls -->
      <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b" style="border-color:var(--border)">
        <select id="cat-page-length" class="border rounded-lg px-2 py-1.5 text-xs outline-none"
          style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option value="10">10</option>
          <option value="25" selected>25</option>
          <option value="50">50</option>
        </select>
        <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 flex-1 min-w-[160px] max-w-xs"
          style="background:var(--muted)">
          <SearchIcon :size="13" style="color:var(--muted-foreground)" />
          <input id="cat-search" type="text" placeholder="Type to Search..."
            class="flex-1 bg-transparent text-xs outline-none" style="color:var(--foreground)" />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto scrollbar-thin">
        <table id="categories-table" class="w-full text-sm" style="border-collapse:collapse">
          <thead>
            <tr class="border-b"
              style="border-color:var(--border);background:color-mix(in srgb, var(--muted) 30%, transparent)">
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap"
                style="color:var(--muted-foreground)">#</th>
              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer select-none"
                style="color:var(--muted-foreground)">Category Name</th>
              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer select-none"
                style="color:var(--muted-foreground)">Description</th>
              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer select-none"
                style="color:var(--muted-foreground)">Last Accessed By</th>
              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer select-none"
                style="color:var(--muted-foreground)">Updated At</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap"
                style="color:var(--muted-foreground)">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between gap-3 px-4 py-3 border-t" style="border-color:var(--border)">
        <span id="cat-info" class="text-xs" style="color:var(--muted-foreground)"></span>
        <div id="cat-paging" class="flex items-center gap-1"></div>
      </div>
    </div>

    <CategoryFormModal v-if="showFormModal" :category="editingCategory" @close="closeFormModal" @save="saveCategory" />
    <CategoryDeleteModal v-if="deletingCategory" :category="deletingCategory" @close="deletingCategory = null"
      @confirm="confirmDelete" />
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Plus as PlusIcon, Search as SearchIcon } from '@lucide/vue';
import CategoryFormModal from './CategoryFormModal.vue';
import CategoryDeleteModal from './CategoryDeleteModal.vue';
import { categoryApi } from '@/services/settingsApi';
import { toast } from 'vue-sonner';


const showFormModal = ref(false);
const editingCategory = ref(null);
const deletingCategory = ref(null);

let dtInstance = null;

onMounted(() => {
  const $ = window.$;

  dtInstance = $('#categories-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '/api/categories', headers: { 'X-Requested-With': 'XMLHttpRequest' } },
    dom: 'rt', // only table + processing, no default controls
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'category_name', name: 'category_name' },
      { data: 'description', name: 'description', defaultContent: '' },
      { data: 'last_accessed_by', name: 'last_accessed_by', defaultContent: '' },
      { data: 'updated_at', name: 'updated_at', render: v => v ? new Date(v).toLocaleString('en-IN') : '' },
      {
        data: null, orderable: false, searchable: false,
        render: d =>
          `<div class="flex items-center gap-1">` +
          `<button class="dt-edit p-1.5 rounded-lg transition-colors" data-id="${d.id}" title="Edit" style="color:var(--primary)">` +
          `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>` +
          `</button>` +
          `<button class="dt-delete p-1.5 rounded-lg transition-colors" data-id="${d.id}" title="Delete" style="color:var(--danger)">` +
          `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>` +
          `</button>` +
          `</div>`,
      },
    ],
    order: [[1, 'asc']],
    pageLength: 25,
    lengthMenu: [10, 25, 50],
    // style tbody rows
    createdRow(row) {
      $(row).css('border-bottom', '1px solid var(--border)');
      $(row).find('td').css({ padding: '10px 16px', 'white-space': 'nowrap', 'font-size': '12px', color: 'var(--foreground)', 'border-top': 'none' });
    },
    drawCallback() {
      renderInfo();
      renderPaging();
    },
  });

  // Wire custom length
  $('#cat-page-length').on('change', function () {
    dtInstance.page.len(+this.value).draw();
  });

  // Wire custom search
  let searchTimer;
  $('#cat-search').on('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => dtInstance.search(this.value).draw(), 400);
  });

  // Row hover
  $('#categories-table tbody').on('mouseenter', 'tr', function () {
    $(this).css('background', 'color-mix(in srgb, var(--muted) 40%, transparent)');
  }).on('mouseleave', 'tr', function () {
    $(this).css('background', '');
  });

  // Edit / Delete
  $('#categories-table').on('click', '.dt-edit', function () {
    const id = $(this).data('id');
    const cat = dtInstance.rows().data().toArray().find(r => r.id == id);
    if (cat) openEdit(cat);
  });
  $('#categories-table').on('click', '.dt-delete', function () {
    const id = $(this).data('id');
    const cat = dtInstance.rows().data().toArray().find(r => r.id == id);
    if (cat) openDelete(cat);
  });
});

function renderInfo() {
  const info = dtInstance.page.info();
  const el = document.getElementById('cat-info');
  if (!el) return;
  if (info.recordsTotal === 0) { el.textContent = 'No entries'; return; }
  el.textContent = `Showing ${info.start + 1} to ${info.end} of ${info.recordsDisplay} entries`;
}

function renderPaging() {
  const info = dtInstance.page.info();
  const el = document.getElementById('cat-paging');
  if (!el) return;
  const total = info.pages, cur = info.page; // 0-based
  const count = Math.min(5, total);
  const start = cur < 2 ? 0 : cur >= total - 2 ? Math.max(0, total - 5) : cur - 2;
  const pages = Array.from({ length: count }, (_, i) => start + i);

  const btnBase = 'display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;border:none;background:none;';

  let html = `<button style="${btnBase}color:var(--muted-foreground)" data-page="first">«</button>`;
  html += `<button style="${btnBase}color:var(--muted-foreground)" data-page="prev">‹</button>`;
  pages.forEach(p => {
    const active = p === cur;
    html += `<button style="${btnBase}${active ? 'background:var(--primary);color:var(--primary-foreground)' : 'color:var(--muted-foreground)'}" data-page="${p}">${p + 1}</button>`;
  });
  html += `<button style="${btnBase}color:var(--muted-foreground)" data-page="next">›</button>`;
  html += `<button style="${btnBase}color:var(--muted-foreground)" data-page="last">»</button>`;
  el.innerHTML = html;

  el.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', () => {
      const p = btn.dataset.page;
      if (p === 'first') dtInstance.page('first').draw('page');
      else if (p === 'prev') dtInstance.page('previous').draw('page');
      else if (p === 'next') dtInstance.page('next').draw('page');
      else if (p === 'last') dtInstance.page('last').draw('page');
      else dtInstance.page(+p).draw('page');
    });
  });
}

onBeforeUnmount(() => {
  if (dtInstance) { dtInstance.destroy(); dtInstance = null; }
});

function openCreate() { editingCategory.value = null; showFormModal.value = true; }
function openEdit(cat) { editingCategory.value = cat; showFormModal.value = true; }
function closeFormModal() { showFormModal.value = false; editingCategory.value = null; }
function openDelete(cat) { deletingCategory.value = cat; }

async function saveCategory(data) {
  try {
    if (editingCategory.value) {
      await categoryApi.update(editingCategory.value.id, data);
      toast.success('Category updated successfully!');
    } else {
      await categoryApi.create(data);
      toast.success('Category created successfully!');
    }
    closeFormModal();
    dtInstance?.ajax.reload(null, false);
  } catch (e) {
    toast.error(e?.errors ? Object.values(e.errors).flat().join(' ') : 'Save failed');
  }
}

async function confirmDelete() {
  try {
    await categoryApi.remove(deletingCategory.value.id);
    toast.success('Category deleted successfully!');
    deletingCategory.value = null;
    dtInstance?.ajax.reload(null, false);
  } catch {
    toast.error('Failed to delete category');
  }
}
</script>

