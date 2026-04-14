<script setup>
import AppButton from '../../ui/AppButton.vue'

const props = defineProps({
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
  iconPath: {
    type: Function,
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
  formatCurrency: {
    type: Function,
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
})

defineEmits([
  'update:selected-order-type',
  'go-back',
  'update-quantity',
  'toggle-flag',
  'update:selected-payment-mode',
])
</script>

<template>
  <aside class="cart-panel">
    <div class="order-type-tabs">
      <AppButton
        v-for="type in props.orderTypes"
        :key="type"
        class="order-type-tab"
        :class="{ 'order-type-tab--active': props.selectedOrderType === type }"
        @click="$emit('update:selected-order-type', type)"
      >
        {{ type }}
      </AppButton>
    </div>

    <div class="cart-top-meta">
      <div class="action-strip">
        <AppButton v-for="tab in props.actionTabs" :key="tab" class="action-strip__button">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path :d="props.iconPath(tab === 'Table' ? 'fork' : tab === 'Guest' ? 'guest' : tab === 'Group' ? 'group' : tab === 'Notes' ? 'note' : 'order')" />
          </svg>
          <span>{{ tab }}</span>
        </AppButton>
      </div>

      <div class="selected-table-card">
        <div>
          <strong>{{ props.selectedTable?.sectionTitle }}</strong>
          <p>Table {{ props.selectedTable?.label }}</p>
        </div>
        <AppButton class="back-link" @click="$emit('go-back')">Change</AppButton>
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
          <AppButton @click="$emit('update-quantity', { itemId: item.id, delta: -1 })">-</AppButton>
          <span>{{ item.quantity }}</span>
          <AppButton @click="$emit('update-quantity', { itemId: item.id, delta: 1 })">+</AppButton>
        </div>
        <strong>{{ props.formatCurrency(item.price * item.quantity) }}</strong>
      </article>
    </div>

    <div class="cart-controls">
      <div class="cart-controls__row">
        <AppButton class="cart-cta cart-cta--red">Bogo Offer</AppButton>
        <AppButton class="cart-cta cart-cta--outline">Split</AppButton>
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
        <AppButton class="bottom-actions__primary">Save</AppButton>
        <AppButton class="bottom-actions__primary">Save & Print</AppButton>
        <AppButton class="bottom-actions__primary">Save & Bill</AppButton>
        <AppButton class="bottom-actions__dark">KOT</AppButton>
        <AppButton class="bottom-actions__dark">KOT & Print</AppButton>
        <AppButton class="bottom-actions__ghost">Hold</AppButton>
      </div>
    </div>
  </aside>
</template>
