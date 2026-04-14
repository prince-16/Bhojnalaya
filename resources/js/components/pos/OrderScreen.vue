<script setup>
const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
  selectedCategoryId: {
    type: String,
    required: true,
  },
  itemSearch: {
    type: String,
    required: true,
  },
  shortCode: {
    type: String,
    required: true,
  },
  filteredItems: {
    type: Array,
    required: true,
  },
  orderTypes: {
    type: Array,
    required: true,
  },
  selectedOrderType: {
    type: String,
    required: true,
  },
  actionTabs: {
    type: Array,
    required: true,
  },
  selectedTable: {
    type: Object,
    default: null,
  },
  cart: {
    type: Array,
    required: true,
  },
  activeCategory: {
    type: Object,
    required: true,
  },
  flags: {
    type: Object,
    required: true,
  },
  totalAmount: {
    type: Number,
    required: true,
  },
  paymentModes: {
    type: Array,
    required: true,
  },
  selectedPaymentMode: {
    type: String,
    required: true,
  },
  iconPath: {
    type: Function,
    required: true,
  },
  formatCurrency: {
    type: Function,
    required: true,
  },
})

defineEmits([
  'select-category',
  'update:item-search',
  'update:short-code',
  'add-item',
  'update:selected-order-type',
  'go-back',
  'update-quantity',
  'toggle-flag',
  'update:selected-payment-mode',
])
</script>

<template>
  <section class="order-layout">
    <aside class="category-sidebar">
      <button class="category-sidebar__active">
        <span>{{ props.activeCategory.name }}</span>
        <span class="category-sidebar__chevron">▾</span>
      </button>

      <button
        v-for="category in props.categories"
        :key="category.id"
        class="category-link"
        :class="{ 'category-link--selected': props.selectedCategoryId === category.id }"
        @click="$emit('select-category', category.id)"
      >
        {{ category.name }}
      </button>
    </aside>

    <section class="order-board">
      <div class="order-filters">
        <label class="search-box search-box--wide">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M10.5 5a5.5 5.5 0 1 0 3.47 9.77l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 10.5 5Zm-4 5.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
          </svg>
          <input :value="props.itemSearch" type="text" placeholder="Search item" @input="$emit('update:item-search', $event.target.value)" />
        </label>

        <input
          :value="props.shortCode"
          class="short-code-input"
          type="text"
          placeholder="Short Code"
          @input="$emit('update:short-code', $event.target.value)"
        />
      </div>

      <div class="item-grid">
        <button
          v-for="item in props.filteredItems"
          :key="item.id"
          class="menu-item-card"
          :class="`menu-item-card--${item.accent}`"
          @click="$emit('add-item', item)"
        >
          <span>{{ item.name }}</span>
          <strong v-if="item.price > 0">{{ props.formatCurrency(item.price) }}</strong>
        </button>
      </div>

      <div v-if="props.filteredItems.length === 0" class="item-grid__empty">
        No items available in this category.
      </div>
    </section>

    <aside class="cart-panel">
      <div class="order-type-tabs">
        <button
          v-for="type in props.orderTypes"
          :key="type"
          class="order-type-tab"
          :class="{ 'order-type-tab--active': props.selectedOrderType === type }"
          @click="$emit('update:selected-order-type', type)"
        >
          {{ type }}
        </button>
      </div>

      <div class="cart-top-meta">
        <div class="action-strip">
          <button v-for="tab in props.actionTabs" :key="tab" class="action-strip__button">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path :d="props.iconPath(tab === 'Table' ? 'fork' : tab === 'Guest' ? 'guest' : tab === 'Group' ? 'group' : tab === 'Notes' ? 'note' : 'order')" />
            </svg>
            <span>{{ tab }}</span>
          </button>
        </div>

        <div class="selected-table-card">
          <div>
            <strong>{{ props.selectedTable?.sectionTitle }}</strong>
            <p>Table {{ props.selectedTable?.label }}</p>
          </div>
          <button class="back-link" @click="$emit('go-back')">Change</button>
        </div>
      </div>

      <div class="cart-table-header">
        <span>ITEMS</span>
        <span>CHECK ITEMS</span>
        <span>QTY.</span>
        <span>PRICE</span>
      </div>

      <div v-if="props.cart.length === 0" class="empty-cart">
        <div class="empty-cart__icon">🍽</div>
        <strong>No Item Selected</strong>
        <p>Please Select Item from Left Menu Item</p>
      </div>

      <div v-else class="cart-items">
        <article v-for="item in props.cart" :key="item.id" class="cart-item">
          <div>
            <strong>{{ item.name }}</strong>
            <p>Selected from {{ props.activeCategory.name }}</p>
          </div>
          <label class="cart-check">
            <input type="checkbox" />
          </label>
          <div class="quantity-stepper">
            <button @click="$emit('update-quantity', { itemId: item.id, delta: -1 })">-</button>
            <span>{{ item.quantity }}</span>
            <button @click="$emit('update-quantity', { itemId: item.id, delta: 1 })">+</button>
          </div>
          <strong>{{ props.formatCurrency(item.price * item.quantity) }}</strong>
        </article>
      </div>

      <div class="cart-controls">
        <div class="cart-controls__row">
          <button class="cart-cta cart-cta--red">Bogo Offer</button>
          <button class="cart-cta cart-cta--outline">Split</button>
          <label class="check-flag">
            <input :checked="props.flags.complimentary" type="checkbox" @change="$emit('toggle-flag', { key: 'complimentary', value: $event.target.checked })" />
            <span>Complimentary</span>
          </label>
          <div class="cart-total">Total <strong>{{ props.formatCurrency(props.totalAmount) }}</strong></div>
        </div>

        <div class="cart-controls__row cart-controls__row--payments">
          <label v-for="mode in props.paymentModes" :key="mode" class="payment-radio">
            <input
              :checked="props.selectedPaymentMode === mode"
              type="radio"
              :value="mode"
              @change="$emit('update:selected-payment-mode', mode)"
            />
            <span>{{ mode }}</span>
          </label>
        </div>

        <div class="cart-controls__row cart-controls__row--flags">
          <label class="check-flag">
            <input :checked="props.flags.paid" type="checkbox" @change="$emit('toggle-flag', { key: 'paid', value: $event.target.checked })" />
            <span>It's Paid</span>
          </label>
          <label class="check-flag">
            <input :checked="props.flags.loyalty" type="checkbox" @change="$emit('toggle-flag', { key: 'loyalty', value: $event.target.checked })" />
            <span>Loyalty</span>
          </label>
          <label class="check-flag">
            <input
              :checked="props.flags.feedbackSms"
              type="checkbox"
              @change="$emit('toggle-flag', { key: 'feedbackSms', value: $event.target.checked })"
            />
            <span>Send Feedback SMS</span>
          </label>
        </div>

        <div class="bottom-actions">
          <button class="bottom-actions__primary">Save</button>
          <button class="bottom-actions__primary">Save & Print</button>
          <button class="bottom-actions__primary">Save & Bill</button>
          <button class="bottom-actions__dark">KOT</button>
          <button class="bottom-actions__dark">KOT & Print</button>
          <button class="bottom-actions__ghost">Hold</button>
        </div>
      </div>
    </aside>
  </section>
</template>
