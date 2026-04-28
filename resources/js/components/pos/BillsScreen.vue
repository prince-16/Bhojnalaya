<script setup>
import AppButton from '../ui/AppButton.vue'

const props = defineProps({
  bills: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
  formatCurrency: {
    type: Function,
    required: true,
  },
})

defineEmits(['refresh', 'reopen-bill'])

function formatStatus(status) {
  const value = String(status ?? '').trim()

  return value ? value.charAt(0).toUpperCase() + value.slice(1) : 'Unknown'
}

function formatDate(value) {
  const date = new Date(value)

  if (Number.isNaN(date.getTime())) {
    return 'Unknown'
  }

  return new Intl.DateTimeFormat('en-IN', {
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date)
}
</script>

<template>
  <section class="content-shell">
    <div class="content-header">
      <div>
        <p class="eyebrow">Bills</p>
      </div>

      <div class="content-header__right">
        <AppButton class="header-action" @click="$emit('refresh')">Refresh Bills</AppButton>
      </div>
    </div>

    <p v-if="props.loading" class="eyebrow">Loading bills...</p>
    <p v-else-if="props.error" class="eyebrow">{{ props.error }}</p>

    <div v-else class="orders-list">
      <article v-for="bill in props.bills" :key="bill.id" class="order-card">
        <div class="order-card__main">
          <div>
            <strong>{{ bill.order_number }}</strong>
            <p>{{ bill.table?.name ? `Table ${bill.table.name}` : 'No table assigned' }}</p>
            <p>{{ bill.table?.floor || 'Unassigned floor' }}</p>
          </div>

          <div>
            <p>Status: <strong>{{ formatStatus(bill.status) }}</strong></p>
            <p>Type: {{ bill.order_type }}</p>
            <p>Mode: {{ bill.payment_mode || 'Cash' }}</p>
            <p>Generated: {{ formatDate(bill.updated_at) }}</p>
          </div>
        </div>

        <div class="order-card__actions">
          <span class="order-card__total">{{ props.formatCurrency(Number(bill.total ?? 0)) }}</span>
          <AppButton class="header-action header-action--solid" @click="$emit('reopen-bill', bill)">Reopen</AppButton>
        </div>
      </article>

      <p v-if="!props.bills.length" class="eyebrow">No bills found.</p>
    </div>
  </section>
</template>
