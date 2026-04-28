<script setup>
import AppButton from '../../ui/AppButton.vue'
import { ref } from 'vue'

const otherModalOpen = ref(false)
const otherSubMode = ref('PhonePe')
const otherLocalNote = ref('')

const otherSubModes = ['PhonePe', 'Google Pay', 'Paytm', 'UPI', 'NEFT/RTGS', 'Cheque', 'Other']

function parseOtherPaymentNote(value) {
  const rawValue = String(value ?? '').trim()

  if (!rawValue) {
    return {
      subMode: 'PhonePe',
      note: '',
    }
  }

  const separatorIndex = rawValue.indexOf(':')

  if (separatorIndex === -1) {
    return {
      subMode: otherSubModes.includes(rawValue) ? rawValue : 'Other',
      note: otherSubModes.includes(rawValue) ? '' : rawValue,
    }
  }

  const subMode = rawValue.slice(0, separatorIndex).trim()
  const note = rawValue.slice(separatorIndex + 1).trim()

  return {
    subMode: otherSubModes.includes(subMode) ? subMode : 'Other',
    note,
  }
}

function openOtherPaymentModal() {
  const parsedValue = parseOtherPaymentNote(props.otherNote)

  otherLocalNote.value = parsedValue.note
  otherSubMode.value = parsedValue.subMode
  otherModalOpen.value = true
}

function handlePaymentModeChange(mode) {
  emit('update:selected-payment-mode', mode)
  if (mode === 'Other') {
    openOtherPaymentModal()
  } else {
    emit('update:other-note', '')
  }
}

function confirmOtherPayment() {
  otherModalOpen.value = false
  emit('update:selected-payment-mode', 'Other')
  emit('update:other-note', `${otherSubMode.value}${otherLocalNote.value ? ': ' + otherLocalNote.value : ''}`)
}

function cancelOtherPayment() {
  otherModalOpen.value = false
}

function getTableNumberLabel(selectedTable) {
  const label = String(selectedTable?.label ?? '').trim()
  return label || 'Table'
}

function getFloorLabel(selectedTable, tableDetails) {
  const floor = String(tableDetails?.floor ?? selectedTable?.sectionTitle ?? '').trim()
  return floor || 'Unassigned'
}

const props = defineProps({
  orderTypes: { type: Array, required: true },
  selectedOrderType: { type: String, required: true },
  actionTabs: { type: Array, required: true },
  iconPath: { type: Function, required: true },
  selectedTable: { type: Object, default: null },
  tableDetails: { type: Object, default: null },
  orderSaveSubmitting: { type: Boolean, default: false },
  orderSaveMessage: { type: String, default: '' },
  orderSaveError: { type: String, default: '' },
  cart: { type: Array, required: true },
  activeCategory: { type: Object, required: true },
  formatCurrency: { type: Function, required: true },
  flags: { type: Object, required: true },
  totalAmount: { type: Number, required: true },
  paymentModes: { type: Array, required: true },
  selectedPaymentMode: { type: String, required: true },
  otherNote: { type: String, default: '' },
})

const emit = defineEmits([
  'update:selected-order-type',
  'go-back',
  'open-table-switcher',
  'save-order',
  'update-quantity',
  'toggle-flag',
  'update:selected-payment-mode',
  'update:other-note',
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
        <AppButton v-for="tab in props.actionTabs" :key="tab" class="action-strip__button" @click="tab === 'Table' ? $emit('open-table-switcher') : null">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path :d="props.iconPath(tab === 'Table' ? 'fork' : tab === 'Guest' ? 'guest' : tab === 'Group' ? 'group' : tab === 'Notes' ? 'note' : 'order')" />
          </svg>
          <span>{{ tab === 'Table' ? getTableNumberLabel(props.selectedTable) : tab }}</span>
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
            name="payment-mode"
            :value="mode"
            @click="handlePaymentModeChange(mode)"
          />
          <span>{{ mode }}</span>
        </label>
      </div>

      <div v-if="props.selectedPaymentMode === 'Other' && props.otherNote" class="other-note-badge">
        {{ props.otherNote }}
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
        <AppButton class="bottom-actions__primary" :disabled="props.orderSaveSubmitting" @click="$emit('save-order')">{{ props.orderSaveSubmitting ? 'Saving...' : 'Save' }}</AppButton>
        <AppButton class="bottom-actions__primary">Save &amp; Print</AppButton>
        <AppButton class="bottom-actions__primary">Save &amp; Bill</AppButton>
        <AppButton class="bottom-actions__dark">KOT</AppButton>
        <AppButton class="bottom-actions__dark">KOT &amp; Print</AppButton>
        <AppButton class="bottom-actions__ghost">Hold</AppButton>
      </div>
    </div>
  </aside>

  <!-- Other Payment Modal -->
  <div v-if="otherModalOpen" class="other-payment-overlay" @click.self="cancelOtherPayment">
    <div class="other-payment-modal">
      <div class="other-payment-modal__header">
        <h3>Other Payment Type</h3>
        <button class="other-payment-modal__close" @click="cancelOtherPayment">&times;</button>
      </div>

      <div class="other-payment-modal__body">
        <label class="other-payment-modal__label">Other Payment Type</label>
        <select v-model="otherSubMode" class="other-payment-modal__select">
          <option v-for="sub in otherSubModes" :key="sub" :value="sub">{{ sub }}</option>
        </select>

        <label class="other-payment-modal__label">Note (optional)</label>
        <textarea v-model="otherLocalNote" class="other-payment-modal__textarea" placeholder="Add a note..." rows="3" />
      </div>

      <div class="other-payment-modal__footer">
        <AppButton class="header-action" @click="cancelOtherPayment">No</AppButton>
        <AppButton class="header-action header-action--solid" @click="confirmOtherPayment">Yes</AppButton>
      </div>
    </div>
  </div>
</template>
