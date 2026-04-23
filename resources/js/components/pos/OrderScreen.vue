<script setup>
import OrderCartPanel from './order/OrderCartPanel.vue'
import OrderCategorySidebar from './order/OrderCategorySidebar.vue'
import OrderItemBoard from './order/OrderItemBoard.vue'

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
  menuItemsLoading: {
    type: Boolean,
    default: false,
  },
  menuItemsError: {
    type: String,
    default: '',
  },
})

defineEmits([
  'select-category',
  'update:item-search',
  'update:short-code',
  'add-item',
  'add-menu-item',
  'edit-menu-item',
  'delete-menu-item',
  'update:selected-order-type',
  'go-back',
  'open-table-switcher',
  'save-order',
  'update-quantity',
  'toggle-flag',
  'update:selected-payment-mode',
])
</script>

<template>
  <section class="order-layout">
    <OrderCategorySidebar
      :categories="props.categories"
      :selected-category-id="props.selectedCategoryId"
      :active-category="props.activeCategory"
      @select-category="$emit('select-category', $event)"
    />

    <OrderItemBoard
      :item-search="props.itemSearch"
      :short-code="props.shortCode"
      :filtered-items="props.filteredItems"
      :format-currency="props.formatCurrency"
      :menu-items-loading="props.menuItemsLoading"
      :menu-items-error="props.menuItemsError"
      @update:item-search="$emit('update:item-search', $event)"
      @update:short-code="$emit('update:short-code', $event)"
      @add-item="$emit('add-item', $event)"
      @add-menu-item="$emit('add-menu-item')"
      @edit-menu-item="$emit('edit-menu-item', $event)"
      @delete-menu-item="$emit('delete-menu-item', $event)"
    />

    <OrderCartPanel
      :order-types="props.orderTypes"
      :selected-order-type="props.selectedOrderType"
      :action-tabs="props.actionTabs"
      :icon-path="props.iconPath"
      :selected-table="props.selectedTable"
      :table-details="props.tableDetails"
      :order-save-submitting="props.orderSaveSubmitting"
      :order-save-message="props.orderSaveMessage"
      :order-save-error="props.orderSaveError"
      :cart="props.cart"
      :active-category="props.activeCategory"
      :format-currency="props.formatCurrency"
      :flags="props.flags"
      :total-amount="props.totalAmount"
      :payment-modes="props.paymentModes"
      :selected-payment-mode="props.selectedPaymentMode"
      @update:selected-order-type="$emit('update:selected-order-type', $event)"
      @go-back="$emit('go-back')"
      @open-table-switcher="$emit('open-table-switcher')"
      @save-order="$emit('save-order')"
      @update-quantity="$emit('update-quantity', $event)"
      @toggle-flag="$emit('toggle-flag', $event)"
      @update:selected-payment-mode="$emit('update:selected-payment-mode', $event)"
    />
  </section>
</template>
