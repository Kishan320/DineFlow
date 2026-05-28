<template>
  <div class="grid-root">

    <!-- Top bar: search -->
    <div class="grid-topbar">
      <div class="search-box">
        <SearchIcon :size="15" class="search-icon" />
        <input
          v-model="search"
          type="text"
          placeholder="Search menu items..."
          class="search-input"
        />
        <button v-if="search" @click="search = ''" class="search-clear">
          <XIcon :size="13" />
        </button>
      </div>
    </div>

    <!-- Category pills -->
    <div class="cat-bar">
      <button
        v-for="cat in categories"
        :key="cat.id"
        class="cat-pill"
        :class="{ active: activeCategory === cat.id }"
        @click="activeCategory = cat.id"
      >
        <span class="cat-icon">{{ cat.icon }}</span>
        <span>{{ cat.label }}</span>
      </button>
    </div>

    <!-- Results count -->
    <div class="results-bar">
      <span class="results-text">{{ filtered.length }} item{{ filtered.length !== 1 ? 's' : '' }}</span>
    </div>

    <!-- Item grid -->
    <div class="items-scroll scrollbar-thin">
      <div v-if="filtered.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <AlertCircleIcon :size="28" />
        </div>
        <p class="empty-title">No items found</p>
        <p class="empty-sub">Try a different search or category</p>
        <button @click="search = ''; activeCategory = 'cat-all'" class="empty-reset">Clear filters</button>
      </div>

      <div v-else class="items-grid">
        <div
          v-for="item in filtered"
          :key="item.id"
          class="item-card"
          :class="{ unavailable: !item.available }"
          @click="item.available && $emit('add-to-cart', item)"
        >
          <!-- Image -->
          <div class="item-img-wrap">
            <img :src="item.image" :alt="item.imageAlt" class="item-img" />
            <span v-if="item.popular" class="item-badge-popular">⭐ Popular</span>
            <div v-if="!item.available" class="item-unavail-overlay">
              <span>Unavailable</span>
            </div>
          </div>

          <!-- Info -->
          <div class="item-info">
            <p class="item-name">{{ item.name }}</p>
            <p class="item-desc">{{ item.description }}</p>
            <div class="item-footer">
              <span class="item-price">${{ item.price.toFixed(2) }}</span>
              <button
                v-if="item.available"
                class="item-add-btn"
                @click.stop="$emit('add-to-cart', item)"
                :title="`Add ${item.name}`"
              >
                <PlusIcon :size="14" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Search as SearchIcon, X as XIcon, AlertCircle as AlertCircleIcon, Plus as PlusIcon } from '@lucide/vue';

const props = defineProps({ items: Array, categories: Array });
defineEmits(['add-to-cart']);

const activeCategory = ref('cat-all');
const search = ref('');

const filtered = computed(() =>
  props.items.filter(item => {
    const catLabel = props.categories.find(c => c.id === activeCategory.value)?.label;
    const matchCat = activeCategory.value === 'cat-all' || item.category === catLabel;
    const matchSearch = item.name.toLowerCase().includes(search.value.toLowerCase());
    return matchCat && matchSearch;
  })
);
</script>

<style scoped>
.grid-root {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: var(--background);
}

/* ── Top search bar ── */
.grid-topbar {
  padding: 12px 16px 10px;
  background: var(--card);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--muted);
  border-radius: 10px;
  padding: 9px 12px;
}
.search-icon { color: var(--muted-foreground); flex-shrink: 0; }
.search-input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  font-size: 13px;
  color: var(--foreground);
  font-family: var(--font-sans);
}
.search-input::placeholder { color: var(--muted-foreground); }
.search-clear {
  color: var(--muted-foreground);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
}
.search-clear:hover { color: var(--foreground); }

/* ── Category pills ── */
.cat-bar {
  display: flex;
  gap: 6px;
  padding: 10px 16px;
  overflow-x: auto;
  scrollbar-width: none;
  background: var(--card);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.cat-bar::-webkit-scrollbar { display: none; }
.cat-pill {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 6px 14px;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  cursor: pointer;
  border: 1.5px solid transparent;
  background: var(--muted);
  color: var(--muted-foreground);
  transition: all .15s;
  font-family: var(--font-sans);
}
.cat-pill:hover { background: var(--border); color: var(--foreground); }
.cat-pill.active {
  background: var(--primary);
  color: var(--primary-foreground);
  border-color: var(--primary);
}
.cat-icon { font-size: 14px; }

/* ── Results bar ── */
.results-bar {
  padding: 8px 16px 4px;
  flex-shrink: 0;
}
.results-text {
  font-size: 11px;
  font-weight: 600;
  color: var(--muted-foreground);
  text-transform: uppercase;
  letter-spacing: .05em;
}

/* ── Scrollable grid area ── */
.items-scroll {
  flex: 1;
  overflow-y: auto;
  padding: 8px 16px 16px;
}

/* ── Grid ── */
.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
  gap: 12px;
}

/* ── Item card ── */
.item-card {
  background: var(--card);
  border: 1.5px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  cursor: pointer;
  transition: box-shadow .15s, border-color .15s, transform .15s;
}
.item-card:hover {
  border-color: var(--primary);
  box-shadow: 0 4px 20px rgba(0,0,0,.08);
  transform: translateY(-2px);
}
.item-card.unavailable {
  opacity: .55;
  cursor: not-allowed;
}
.item-card.unavailable:hover {
  border-color: var(--border);
  box-shadow: none;
  transform: none;
}

/* Image */
.item-img-wrap {
  position: relative;
  height: 130px;
  overflow: hidden;
}
.item-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .2s;
}
.item-card:hover .item-img { transform: scale(1.05); }

.item-badge-popular {
  position: absolute;
  top: 8px;
  left: 8px;
  background: var(--primary);
  color: var(--primary-foreground);
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 99px;
}
.item-unavail-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,.45);
  display: flex;
  align-items: center;
  justify-content: center;
}
.item-unavail-overlay span {
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .04em;
}

/* Info */
.item-info {
  padding: 10px 10px 10px;
}
.item-name {
  font-size: 12px;
  font-weight: 700;
  color: var(--foreground);
  line-height: 1.3;
  margin-bottom: 3px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.item-desc {
  font-size: 10px;
  color: var(--muted-foreground);
  line-height: 1.4;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.item-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.item-price {
  font-size: 15px;
  font-weight: 800;
  color: var(--primary);
  font-variant-numeric: tabular-nums;
}
.item-add-btn {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--primary);
  color: var(--primary-foreground);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: opacity .15s, transform .1s;
  flex-shrink: 0;
}
.item-add-btn:hover { opacity: .88; }
.item-add-btn:active { transform: scale(.92); }

/* ── Empty state ── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}
.empty-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: var(--muted);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted-foreground);
  margin-bottom: 14px;
}
.empty-title { font-size: 14px; font-weight: 700; color: var(--foreground); margin-bottom: 4px; }
.empty-sub   { font-size: 12px; color: var(--muted-foreground); margin-bottom: 14px; }
.empty-reset {
  font-size: 12px;
  font-weight: 600;
  color: var(--primary);
  background: none;
  border: none;
  cursor: pointer;
  text-decoration: underline;
  font-family: var(--font-sans);
}
</style>
