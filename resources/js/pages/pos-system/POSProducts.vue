<template>
  <div class="products-root">

    <!-- Top bar -->
    <div class="products-topbar">
      <div class="topbar-left">
        <h1 class="pos-title">Point of Sale</h1>
      </div>
      <div class="topbar-meta">
        <span v-if="session" class="meta-chip">
          <span class="meta-dot" />Session: {{ session.session_code }}
        </span>
        <span class="meta-chip"><LayoutGridIcon :size="13" /> {{ session?.counter_name ?? 'Main Counter' }}</span>
        <span class="meta-chip"><BuildingIcon :size="13" /> {{ session?.branch_name ?? 'Main Branch' }}</span>
        <button class="destroy-btn" @click="$emit('destroy-session')">
          <Trash2Icon :size="13" /> Destroy Session
        </button>
      </div>
    </div>

    <!-- Search + filter bar -->
    <div class="search-bar">
      <div class="search-input-wrap">
        <SearchIcon :size="15" class="search-ico" />
        <input
          v-model="search"
          type="text"
          placeholder="Search products by name or SKU..."
          class="search-inp"
          @input="onSearch"
        />
        <button v-if="search" @click="search = ''; onSearch()" class="search-x"><XIcon :size="13" /></button>
      </div>
      <button class="search-submit" @click="onSearch"><SearchIcon :size="15" /></button>

      <!-- Category dropdown -->
      <div class="filter-dropdown" @click.stop>
        <button class="filter-btn" @click="catOpen = !catOpen">
          {{ activeCatLabel }} <ChevronDownIcon :size="13" />
        </button>
        <div v-if="catOpen" class="dropdown-menu">
          <button
            v-for="cat in categories"
            :key="cat.id"
            class="dropdown-item"
            :class="{ active: activeCategory === cat.id }"
            @click="activeCategory = cat.id; catOpen = false; onSearch()"
          >{{ cat.label }}</button>
        </div>
      </div>
    </div>

    <!-- Product grid -->
    <div class="products-scroll scrollbar-thin" @click="catOpen = false">
      <!-- Loading -->
      <div v-if="loading" class="empty-state">
        <div class="spinner-lg" />
        <p>Loading products...</p>
      </div>

      <!-- Empty -->
      <div v-else-if="products.length === 0" class="empty-state">
        <PackageIcon :size="40" />
        <p>No products found</p>
        <button @click="resetFilters">Clear filters</button>
      </div>

      <!-- Grid -->
      <div v-else class="products-grid">
        <div
          v-for="item in products"
          :key="item.id"
          class="product-card"
          :class="{ 'out-of-stock': !item.available }"
        >
          <div class="card-img-wrap">
            <img :src="item.image" :alt="item.name" class="card-img" loading="lazy" />
            <span v-if="!item.available" class="card-badge-oos">Out of Stock</span>
          </div>

          <div class="card-body">
            <h3 class="card-name">{{ item.name }}</h3>
            <p class="card-sku">SKU: {{ item.code }}</p>

            <div class="card-tags">
              <span class="tag">{{ item.category }}</span>
            </div>

            <div class="card-price-row">
              <span class="card-price">{{ formatCurrency(item.price) }}</span>
              <span class="card-stock" :class="item.available ? 'stock-ok' : 'stock-low'">
                {{ item.available ? 'In Stock' : 'Out of Stock' }}
              </span>
            </div>

            <button
              class="add-btn"
              :disabled="!item.available"
              @click="$emit('add', item)"
            >
              <PlusIcon :size="14" />
              Add to Cart
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="pagination-bar">
        <button
          v-for="p in meta.last_page"
          :key="p"
          class="page-btn"
          :class="{ active: p === meta.current_page }"
          @click="$emit('load-products', { page: p, search: search, category: activeCatLabel !== 'All Categories' ? activeCatLabel : '' })"
        >{{ p }}</button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
  Search as SearchIcon, X as XIcon, ChevronDown as ChevronDownIcon,
  Plus as PlusIcon, Trash2 as Trash2Icon, LayoutGrid as LayoutGridIcon,
  Building2 as BuildingIcon, Package as PackageIcon,
} from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';

const props = defineProps({
  products:   { type: Array,  default: () => [] },
  categories: { type: Array,  default: () => [] },
  meta:       { type: Object, default: () => ({ current_page: 1, last_page: 1, total: 0, per_page: 24 }) },
  loading:    { type: Boolean, default: false },
  session:    { type: Object,  default: null },
});

const emit = defineEmits(['add', 'load-products', 'destroy-session']);

const posStore = usePOSStore();

const search         = ref('');
const activeCategory = ref(null);
const catOpen        = ref(false);

const activeCatLabel = computed(() =>
  props.categories.find(c => c.id === activeCategory.value)?.label ?? 'All Categories'
);

let searchTimer = null;
function onSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    emit('load-products', {
      page: 1,
      search: search.value,
      category: activeCategory.value
        ? (props.categories.find(c => c.id === activeCategory.value)?.label ?? '')
        : '',
    });
  }, 350);
}

function resetFilters() {
  search.value         = '';
  activeCategory.value = null;
  emit('load-products', { page: 1 });
}

function formatCurrency(val) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}
</script>

<style scoped>
.products-root {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: var(--background);
}

.products-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  flex-wrap: wrap;
  gap: 8px;
}
.pos-title { font-size: 18px; font-weight: 800; color: var(--foreground); margin: 0; }
.topbar-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.meta-chip {
  display: flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 600;
  padding: 4px 10px; border-radius: 20px;
  background: var(--muted); color: var(--muted-foreground);
  border: 1px solid var(--border);
}
.meta-dot { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; }
.destroy-btn {
  display: flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700;
  padding: 4px 12px; border-radius: 20px;
  background: none; color: var(--danger);
  border: 1.5px solid var(--danger);
  cursor: pointer; transition: all .15s;
  font-family: var(--font-sans);
}
.destroy-btn:hover { background: var(--danger); color: #fff; }

.search-bar {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 16px; border-bottom: 1px solid var(--border);
  flex-shrink: 0; flex-wrap: wrap;
}
.search-input-wrap {
  flex: 1; min-width: 200px;
  display: flex; align-items: center; gap: 8px;
  background: var(--muted); border-radius: 10px; padding: 8px 12px;
  border: 1px solid var(--border);
}
.search-ico { color: var(--muted-foreground); flex-shrink: 0; }
.search-inp {
  flex: 1; background: transparent; border: none; outline: none;
  font-size: 13px; color: var(--foreground); font-family: var(--font-sans);
}
.search-inp::placeholder { color: var(--muted-foreground); }
.search-x { background: none; border: none; cursor: pointer; color: var(--muted-foreground); display: flex; align-items: center; }
.search-submit {
  width: 36px; height: 36px; border-radius: 10px;
  background: var(--primary); color: var(--primary-foreground);
  border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}

.filter-dropdown { position: relative; flex-shrink: 0; }
.filter-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 10px;
  border: 1px solid var(--border); background: var(--muted);
  color: var(--foreground); font-size: 12px; font-weight: 600;
  cursor: pointer; white-space: nowrap; font-family: var(--font-sans);
}
.filter-btn:hover { border-color: var(--primary); }
.dropdown-menu {
  position: absolute; top: calc(100% + 4px); left: 0; z-index: 50;
  min-width: 180px; background: var(--card);
  border: 1px solid var(--border); border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0,0,0,.12); padding: 4px;
  max-height: 260px; overflow-y: auto;
}
.dropdown-item {
  width: 100%; text-align: left; padding: 8px 12px; border-radius: 8px;
  font-size: 12px; font-weight: 500; color: var(--foreground);
  background: none; border: none; cursor: pointer; font-family: var(--font-sans);
}
.dropdown-item:hover { background: var(--muted); }
.dropdown-item.active { background: var(--accent); color: var(--primary); font-weight: 700; }

.products-scroll {
  flex: 1; overflow-y: auto; padding: 12px 16px;
}
.empty-state {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; min-height: 200px; gap: 10px;
  color: var(--muted-foreground); text-align: center;
}
.empty-state button {
  font-size: 12px; font-weight: 600; color: var(--primary);
  background: none; border: none; cursor: pointer; text-decoration: underline;
  font-family: var(--font-sans);
}
.spinner-lg {
  width: 36px; height: 36px;
  border: 3px solid var(--border); border-top-color: var(--primary);
  border-radius: 50%; animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 14px;
}
.product-card {
  background: var(--card); border: 1.5px solid var(--border);
  border-radius: 14px; overflow: hidden;
  transition: box-shadow .15s, border-color .15s, transform .15s;
}
.product-card:hover { border-color: var(--primary); box-shadow: 0 4px 20px rgba(0,0,0,.08); transform: translateY(-2px); }
.product-card.out-of-stock { opacity: .55; }
.card-img-wrap { position: relative; height: 140px; overflow: hidden; }
.card-img { width: 100%; height: 100%; object-fit: cover; transition: transform .2s; }
.product-card:hover .card-img { transform: scale(1.05); }
.card-badge-oos {
  position: absolute; top: 8px; right: 8px;
  background: #ef4444; color: #fff;
  font-size: 10px; font-weight: 700;
  padding: 2px 8px; border-radius: 99px;
}
.card-body { padding: 10px 12px 12px; }
.card-name { font-size: 13px; font-weight: 700; color: var(--foreground); margin: 0 0 3px; line-height: 1.3; }
.card-sku  { font-size: 10px; color: var(--muted-foreground); margin: 0 0 6px; }
.card-tags { display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 8px; }
.tag {
  font-size: 10px; font-weight: 600; padding: 2px 8px;
  border-radius: 99px; background: var(--muted); color: var(--muted-foreground);
}
.card-price-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.card-price { font-size: 16px; font-weight: 800; color: var(--primary); font-variant-numeric: tabular-nums; }
.card-stock { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 99px; }
.stock-ok  { background: #dcfce7; color: #16a34a; }
.stock-low { background: #fee2e2; color: #dc2626; }
.add-btn {
  width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px;
  padding: 9px; border-radius: 10px;
  background: var(--primary); color: var(--primary-foreground);
  border: none; cursor: pointer; font-size: 13px; font-weight: 700;
  transition: opacity .15s; font-family: var(--font-sans);
}
.add-btn:hover:not(:disabled) { opacity: .88; }
.add-btn:disabled { opacity: .4; cursor: not-allowed; }

.pagination-bar {
  display: flex; align-items: center; justify-content: center;
  gap: 4px; padding: 12px 0;
}
.page-btn {
  width: 32px; height: 32px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--muted);
  color: var(--foreground); font-size: 12px; font-weight: 600;
  cursor: pointer; font-family: var(--font-sans);
}
.page-btn.active { background: var(--primary); color: var(--primary-foreground); border-color: var(--primary); }
</style>
