<script setup>
import AppButton from '../ui/AppButton.vue'

const props = defineProps({
  orders: {
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

defineEmits(['refresh', 'open-order'])

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
        <p class="eyebrow">Orders</p>
      </div>

      <div class="content-header__right">
        <AppButton class="header-action" @click="$emit('refresh')">Refresh Orders</AppButton>
      </div>
    </div>

    <p v-if="props.loading" class="eyebrow">Loading orders...</p>
    <p v-else-if="props.error" class="eyebrow">{{ props.error }}</p>

    <div v-else class="orders-list">
      <article v-for="order in props.orders" :key="order.id" class="order-card">
        <div class="order-card__main">
          <div>
            <strong>{{ order.order_number }}</strong>
            <p>{{ order.table?.name ? `Table ${order.table.name}` : 'No table assigned' }}</p>
            <p>{{ order.table?.floor || 'Unassigned floor' }}</p>
          </div>

          <div>
            <p>Status: <strong>{{ formatStatus(order.status) }}</strong></p>
            <p>Type: {{ order.order_type }}</p>
            <p>Mode: {{ order.payment_mode || 'Cash' }}</p>
            <p>Updated: {{ formatDate(order.updated_at) }}</p>
          </div>
        </div>

        <div class="order-card__actions">
          <span class="order-card__total">{{ props.formatCurrency(Number(order.total ?? 0)) }}</span>
          <AppButton class="header-action header-action--solid" @click="$emit('open-order', order)">Open</AppButton>
        </div>
      </article>

      <p v-if="!props.orders.length" class="eyebrow">No orders found.</p>
    </div>
  </section>
</template>