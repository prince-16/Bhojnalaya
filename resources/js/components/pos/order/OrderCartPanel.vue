<script setup>
import AppButton from '../../ui/AppButton.vue'

function getTableNumberLabel(selectedTable) {
  const label = String(selectedTable?.label ?? '').trim()

  return label || 'Table'
}

function getFloorLabel(selectedTable, tableDetails) {
  const floor = String(tableDetails?.floor ?? selectedTable?.sectionTitle ?? '').trim()

  return floor || 'Unassigned'
}

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
  tableDetails: {
    type: Object,
    default: null,
  },
  orderSaveSubmitting: {
    type: Boolean,
    default: false,
  },
  orderSaveMessage: {
    type: String,
    default: '',
  },
  orderSaveError: {
    type: String,
    default: '',
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
  hasOrderNotes: {
    type: Boolean,
    default: false,
  },
})

defineEmits([
  'update:selected-order-type',
  'go-back',
  'open-table-switcher',
  'open-order-notes',
  'open-item-notes',
  'save-order',
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
        <AppButton
          v-for="tab in props.actionTabs"
          :key="tab"
          class="action-strip__button"
          @click="tab === 'Table' ? $emit('open-table-switcher') : tab === 'Notes' ? $emit('open-order-notes') : null"
        >
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path :d="props.iconPath(tab === 'Table' ? 'fork' : tab === 'Guest' ? 'guest' : tab === 'Group' ? 'group' : tab === 'Notes' ? 'note' : 'order')" />
          </svg>
          <span>{{ tab === 'Table' ? getTableNumberLabel(props.selectedTable) : tab }}</span>
          <span v-if="tab === 'Notes' && props.hasOrderNotes" class="note-indicator">Added</span>
        </AppButton>
      </div>

      <div class="selected-table-card">
        <div>
          <strong>{{ getFloorLabel(props.selectedTable, props.tableDetails) }}</strong>
        </div>
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
      <article v-for="item in props.cart" :key="item.id" class="cart-item cart-item--clickable" @click="$emit('open-item-notes', item.id)">
        <div>
          <div class="cart-item__title-row">
            <strong>{{ item.name }}</strong>
            <span v-if="item.notes" class="note-pill">Note</span>
          </div>
          <p>Selected from {{ props.activeCategory.name }}</p>
          <p v-if="item.notes" class="cart-item__note">Note: {{ item.notes }}</p>
        </div>
        <label class="cart-check">
          <input type="checkbox" @click.stop />
        </label>
        <div class="quantity-stepper">
          <AppButton @click.stop="$emit('update-quantity', { itemId: item.id, delta: -1 })">-</AppButton>
          <span>{{ item.quantity }}</span>
          <AppButton @click.stop="$emit('update-quantity', { itemId: item.id, delta: 1 })">+</AppButton>
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

      <p v-if="props.orderSaveError" class="cart-save-feedback cart-save-feedback--error">{{ props.orderSaveError }}</p>
      <p v-else-if="props.orderSaveMessage" class="cart-save-feedback">{{ props.orderSaveMessage }}</p>

      <div class="bottom-actions">
        <AppButton class="bottom-actions__primary" :disabled="props.orderSaveSubmitting" @click="$emit('save-order', 'save')">{{ props.orderSaveSubmitting ? 'Saving...' : 'Save' }}</AppButton>
        <AppButton class="bottom-actions__primary" :disabled="props.orderSaveSubmitting" @click="$emit('save-order', 'save_and_print')">Save & Print</AppButton>
        <AppButton class="bottom-actions__primary" :disabled="props.orderSaveSubmitting" @click="$emit('save-order', 'save_and_ebill')">Save & eBill</AppButton>
        <AppButton class="bottom-actions__dark" :disabled="props.orderSaveSubmitting" @click="$emit('save-order', 'kot')">KOT</AppButton>
        <AppButton class="bottom-actions__dark" :disabled="props.orderSaveSubmitting" @click="$emit('save-order', 'kot_and_print')">KOT & Print</AppButton>
        <AppButton class="bottom-actions__ghost">Hold</AppButton>
      </div>
    </div>
  </aside>
</template>
