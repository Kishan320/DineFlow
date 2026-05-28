<template>
  <div class="cart-root">

    <!-- Header -->
    <div class="cart-header">
      <div class="cart-header-left">
        <div class="cart-header-icon">
          <ShoppingCartIcon :size="16" />
        </div>
        <div>
          <p class="cart-title">Current Order</p>
          <p class="cart-subtitle">{{ posStore.itemCount }} item{{ posStore.itemCount !== 1 ? 's' : '' }} added</p>
        </div>
      </div>
      <button v-if="posStore.cartItems.length > 0" @click="posStore.clearCart()" class="cart-clear-btn" title="Clear cart">
        <Trash2Icon :size="14" />
      </button>
    </div>

    <!-- Order type -->
    <div class="order-type-bar">
      <button
        v-for="type in orderTypes"
        :key="type"
        class="order-type-btn"
        :class="{ active: posStore.billType === type }"
        @click="posStore.billType = type"
      >{{ type }}</button>
    </div>

    <!-- Order config -->
    <div class="order-config scrollbar-thin">
      <!-- Customer -->
      <div class="field-group">
        <label class="field-label">
          <UserIcon :size="11" /> Customer
        </label>
        <div class="select-wrap">
          <select v-model="posStore.customerId" class="field-select">
            <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <ChevronDownIcon :size="13" class="select-arrow" />
        </div>
      </div>

      <!-- Table + Waiter (Dine In) -->
      <div v-if="posStore.billType === 'Dine In'" class="two-col">
        <div class="field-group">
          <label class="field-label"><TableIcon :size="11" /> Table</label>
          <div class="select-wrap">
            <select v-model="posStore.tableId" class="field-select">
              <option value="">Select</option>
              <option v-for="t in availableTables" :key="t.id" :value="t.id">Table {{ t.number }}</option>
            </select>
            <ChevronDownIcon :size="12" class="select-arrow" />
          </div>
        </div>
        <div class="field-group">
          <label class="field-label"><UserCheckIcon :size="11" /> Waiter</label>
          <div class="select-wrap">
            <select v-model="posStore.waiterId" class="field-select">
              <option value="">Select</option>
              <option v-for="w in availableWaiters" :key="w.id" :value="w.id">{{ w.name.split(' ')[0] }}</option>
            </select>
            <ChevronDownIcon :size="12" class="select-arrow" />
          </div>
        </div>
      </div>

      <!-- Discount -->
      <div class="field-group">
        <label class="field-label"><TagIcon :size="11" /> Discount ($)</label>
        <input
          v-model.number="posStore.discount"
          type="number"
          min="0"
          placeholder="0.00"
          class="field-input"
        />
      </div>
    </div>

    <!-- Divider -->
    <div class="section-divider">
      <span>Order Items</span>
    </div>

    <!-- Cart items -->
    <div class="cart-items scrollbar-thin">
      <div v-if="posStore.cartItems.length === 0" class="cart-empty">
        <div class="cart-empty-icon">
          <ShoppingCartIcon :size="26" />
        </div>
        <p class="cart-empty-title">Cart is empty</p>
        <p class="cart-empty-sub">Tap items on the menu to add them</p>
      </div>

      <div v-else class="cart-list">
        <div
          v-for="ci in posStore.cartItems"
          :key="ci.menuItem.id"
          class="cart-item"
        >
          <!-- Item image thumbnail -->
          <div class="cart-item-thumb">
            <img :src="ci.menuItem.image" :alt="ci.menuItem.name" />
          </div>

          <!-- Name + price -->
          <div class="cart-item-info">
            <p class="cart-item-name">{{ ci.menuItem.name }}</p>
            <p class="cart-item-unit">${{ ci.menuItem.price.toFixed(2) }} each</p>
          </div>

          <!-- Qty controls -->
          <div class="cart-item-qty">
            <button class="qty-btn qty-minus" @click="posStore.updateQty(ci.menuItem.id, -1)">
              <MinusIcon :size="10" />
            </button>
            <span class="qty-val">{{ ci.quantity }}</span>
            <button class="qty-btn qty-plus" @click="posStore.updateQty(ci.menuItem.id, 1)">
              <PlusIcon :size="10" />
            </button>
          </div>

          <!-- Line total + remove -->
          <div class="cart-item-total-wrap">
            <span class="cart-item-total">${{ (ci.menuItem.price * ci.quantity).toFixed(2) }}</span>
            <button class="cart-item-remove" @click="posStore.removeItem(ci.menuItem.id)" title="Remove">
              <XIcon :size="11" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Notes -->
    <div class="notes-area">
      <label class="field-label"><MessageSquareIcon :size="11" /> Order Notes</label>
      <textarea
        v-model="posStore.notes"
        placeholder="Special requests, allergies..."
        rows="2"
        class="notes-input"
      />
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  ShoppingCart as ShoppingCartIcon,
  Minus as MinusIcon,
  Plus as PlusIcon,
  Trash2 as Trash2Icon,
  X as XIcon,
  ChevronDown as ChevronDownIcon,
  User as UserIcon,
  UserCheck as UserCheckIcon,
  Tag as TagIcon,
  MessageSquare as MessageSquareIcon,
  Grid3X3 as TableIcon,
} from '@lucide/vue';
import { usePOSStore } from '@/stores/posStore.js';
import { customers, tables, waiters } from '@/utils/mockData.js';

const posStore = usePOSStore();
const orderTypes = ['Dine In', 'Takeaway', 'Delivery'];
const availableTables  = computed(() => tables.filter(t => t.status === 'Available'));
const availableWaiters = computed(() => waiters.filter(w => w.status === 'Available'));
</script>

<style scoped>
.cart-root {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: var(--card);
}

/* ── Header ── */
.cart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px 12px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.cart-header-left { display: flex; align-items: center; gap: 10px; }
.cart-header-icon {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: var(--accent);
  color: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.cart-title   { font-size: 14px; font-weight: 700; color: var(--foreground); }
.cart-subtitle { font-size: 11px; color: var(--muted-foreground); margin-top: 1px; }
.cart-clear-btn {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  border: 1px solid var(--border);
  background: none;
  color: var(--muted-foreground);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .15s;
}
.cart-clear-btn:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

/* ── Order type ── */
.order-type-bar {
  display: flex;
  gap: 6px;
  padding: 12px 16px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.order-type-btn {
  flex: 1;
  padding: 7px 4px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  border: 1.5px solid var(--border);
  background: var(--muted);
  color: var(--muted-foreground);
  cursor: pointer;
  transition: all .15s;
  font-family: var(--font-sans);
}
.order-type-btn:hover { border-color: var(--primary); color: var(--primary); }
.order-type-btn.active {
  background: var(--primary);
  color: var(--primary-foreground);
  border-color: var(--primary);
}

/* ── Config fields ── */
.order-config {
  padding: 12px 16px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
  overflow-y: auto;
  max-height: 200px;
}
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.field-group { display: flex; flex-direction: column; gap: 4px; }
.field-label {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 600;
  color: var(--muted-foreground);
  text-transform: uppercase;
  letter-spacing: .04em;
}
.select-wrap { position: relative; }
.field-select {
  width: 100%;
  padding: 8px 28px 8px 10px;
  border-radius: 8px;
  border: 1px solid var(--border);
  background: var(--input);
  color: var(--foreground);
  font-size: 12px;
  font-family: var(--font-sans);
  appearance: none;
  outline: none;
  cursor: pointer;
}
.field-select:focus { border-color: var(--primary); }
.select-arrow {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted-foreground);
  pointer-events: none;
}
.field-input {
  width: 100%;
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid var(--border);
  background: var(--input);
  color: var(--foreground);
  font-size: 12px;
  font-family: var(--font-sans);
  outline: none;
}
.field-input:focus { border-color: var(--primary); }

/* ── Section divider ── */
.section-divider {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 16px;
  margin: 10px 0 0;
  flex-shrink: 0;
}
.section-divider::before,
.section-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}
.section-divider span {
  font-size: 10px;
  font-weight: 700;
  color: var(--muted-foreground);
  text-transform: uppercase;
  letter-spacing: .06em;
  white-space: nowrap;
}

/* ── Cart items ── */
.cart-items {
  flex: 1;
  overflow-y: auto;
  padding: 8px 12px;
}
.cart-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  min-height: 140px;
  text-align: center;
  padding: 24px;
}
.cart-empty-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: var(--muted);
  color: var(--muted-foreground);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
  opacity: .6;
}
.cart-empty-title { font-size: 13px; font-weight: 700; color: var(--foreground); margin-bottom: 4px; }
.cart-empty-sub   { font-size: 11px; color: var(--muted-foreground); }

.cart-list { display: flex; flex-direction: column; gap: 6px; }

.cart-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 10px;
  border-radius: 10px;
  background: var(--muted);
  border: 1px solid var(--border);
  transition: border-color .15s;
}
.cart-item:hover { border-color: var(--primary); }

.cart-item-thumb {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}
.cart-item-thumb img { width: 100%; height: 100%; object-fit: cover; }

.cart-item-info { flex: 1; min-width: 0; }
.cart-item-name {
  font-size: 11px;
  font-weight: 700;
  color: var(--foreground);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cart-item-unit { font-size: 10px; color: var(--muted-foreground); margin-top: 1px; }

.cart-item-qty {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
}
.qty-btn {
  width: 22px;
  height: 22px;
  border-radius: 6px;
  border: 1px solid var(--border);
  background: var(--background);
  color: var(--foreground);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .1s;
}
.qty-btn:active { transform: scale(.9); }
.qty-plus { background: var(--primary); color: var(--primary-foreground); border-color: var(--primary); }
.qty-val {
  width: 22px;
  text-align: center;
  font-size: 13px;
  font-weight: 700;
  color: var(--foreground);
  font-variant-numeric: tabular-nums;
}

.cart-item-total-wrap {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 3px;
  flex-shrink: 0;
}
.cart-item-total {
  font-size: 12px;
  font-weight: 700;
  color: var(--foreground);
  font-variant-numeric: tabular-nums;
}
.cart-item-remove {
  width: 18px;
  height: 18px;
  border-radius: 4px;
  border: none;
  background: none;
  color: var(--muted-foreground);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .15s;
}
.cart-item-remove:hover { background: var(--danger); color: #fff; border-radius: 4px; }

/* ── Notes ── */
.notes-area {
  padding: 10px 16px 14px;
  border-top: 1px solid var(--border);
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.notes-input {
  width: 100%;
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid var(--border);
  background: var(--input);
  color: var(--foreground);
  font-size: 12px;
  font-family: var(--font-sans);
  outline: none;
  resize: none;
}
.notes-input:focus { border-color: var(--primary); }
.notes-input::placeholder { color: var(--muted-foreground); }
</style>
