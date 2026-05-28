<template>
  <div class="products-root">

    <!-- ── Top bar ── -->
    <div class="products-topbar">
      <div class="topbar-left">
        <h1 class="pos-title">Point of Sale</h1>
      </div>
      <div class="topbar-meta">
        <span class="meta-chip"><span class="meta-dot" />Session: POS-{{ sessionId }}</span>
        <span class="meta-chip"><LayoutGridIcon :size="13" /> Main Counter</span>
        <span class="meta-chip"><BuildingIcon :size="13" /> Main Branch</span>
        <button class="destroy-btn"><Trash2Icon :size="13" /> Destroy Session</button>
      </div>
    </div>

    <!-- ── Search + filter bar ── -->
    <div class="search-bar">
      <div class="search-input-wrap">
        <SearchIcon :size="15" class="search-ico" />
        <input v-model="search" type="text" placeholder="Search products by name or SKU..." class="search-inp" />
        <button v-if="search" @click="search = ''" class="search-x"><XIcon :size="13" /></button>
      </div>
      <button class="search-submit"><SearchIcon :size="15" /></button>

      <!-- Category dropdown -->
      <div class="filter-dropdown" @click.stop>
        <button class="filter-btn" @click="catOpen = !catOpen">
          {{ activeCatLabel }} <ChevronDownIcon :size="13" />
        </button>
        <div v-if="catOpen" class="dropdown-menu">
          <button
            v-for="cat in allCategories"
            :key="cat.id"
            class="dropdown-item"
            :class="{ active: activeCategory === cat.id }"
            @click="activeCategory = cat.id; catOpen = false"
          >{{ cat.label }}</button>
        </div>
      </div>

      <!-- Brand dropdown -->
      <div class="filter-dropdown" @click.stop>
        <button class="filter-btn" @click="brandOpen = !brandOpen">
          {{ activeBrandLabel }} <ChevronDownIcon :size="13" />
        </button>
        <div v-if="brandOpen" class="dropdown-menu">
          <button
            v-for="b in brands"
            :key="b"
            class="dropdown-item"
            :class="{ active: activeBrand === b }"
            @click="activeBrand = b; brandOpen = false"
          >{{ b }}</button>
        </div>
      </div>
    </div>

    <!-- ── Product grid ── -->
    <div class="products-scroll scrollbar-thin" @click="catOpen = false; brandOpen = false">
      <div v-if="filtered.length === 0" class="empty-state">
        <PackageIcon :size="40" />
        <p>No products found</p>
        <button @click="resetFilters">Clear filters</button>
      </div>

      <div v-else class="products-grid">
        <div
          v-for="item in filtered"
          :key="item.id"
          class="product-card"
          :class="{ 'out-of-stock': !item.available }"
        >
          <!-- Image -->
          <div class="card-img-wrap">
            <img :src="item.image" :alt="item.imageAlt" class="card-img" />
            <span v-if="item.popular" class="card-badge-popular">Popular</span>
            <span v-if="!item.available" class="card-badge-oos">Out of Stock</span>
          </div>

          <!-- Body -->
          <div class="card-body">
            <h3 class="card-name">{{ item.name }}</h3>
            <p class="card-sku">SKU: {{ item.id.toUpperCase() }}</p>

            <!-- Tags -->
            <div class="card-tags">
              <span class="tag">{{ item.category }}</span>
              <span v-if="item.popular" class="tag tag--green">Popular</span>
            </div>

            <!-- Price + stock row -->
            <div class="card-price-row">
              <span class="card-price">${{ item.price.toFixed(2) }}</span>
              <span class="card-stock" :class="item.available ? 'stock-ok' : 'stock-low'">
                {{ item.available ? '50 Piece' : 'Out of Stock' }}
              </span>
            </div>

            <!-- Add to cart -->
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

const props = defineProps({ items: Array, categories: Array });
defineEmits(['add']);

const search         = ref('');
const activeCategory = ref('cat-all');
const activeBrand    = ref('All Brands');
const catOpen        = ref(false);
const brandOpen      = ref(false);

// Random session id
const sessionId = Math.random().toString(36).substring(2, 10).toUpperCase();

const allCategories = computed(() => [
  { id: 'cat-all', label: 'All Categories' },
  ...(props.categories?.filter(c => c.id !== 'cat-all') ?? []),
]);

const brands = ['All Brands', 'Italian', 'Premium', 'Classic', 'House'];

const activeCatLabel  = computed(() => allCategories.value.find(c => c.id === activeCategory.value)?.label ?? 'All Categories');
const activeBrandLabel = computed(() => activeBrand.value);

const filtered = computed(() =>
  props.items.filter(item => {
    const catLabel = props.categories?.find(c => c.id === activeCategory.value)?.label;
    const matchCat = activeCategory.value === 'cat-all' || item.category === catLabel;
    const matchSearch = !search.value || item.name.toLowerCase().includes(search.value.toLowerCase());
    return matchCat && matchSearch;
  })
);

function resetFilters() {
  search.value = '';
  activeCategory.value = 'cat-all';
  activeBrand.value = 'All Brands';
}
</script>


